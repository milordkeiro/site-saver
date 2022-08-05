<?php
namespace App\Libraries;
class Post {
	private $_url;
	
	private $_img_base_url;
	private $_base_url;
    private $_query;    
    private $_post_date;    
    private $_post_content;
    private $_post_title;
    private $_meta_description;
    private $_post_images = array();
    private $_post_anchors = array();
    /**
	 * @param string  $url Url page.
	 * @param string  $query Query String 'div@class=main' 'div@ide=main'
	 * @param string  $img_base_url Url base CDN for images
	 * @return Object. post objet
	 */
    function __construct($url, $query, $img_base_url='https://cdn.esportsvikings.com') {
        //require_once 'dababase.class.php';
        $this->_url = $url;
        $this->_query = $query;
        $this->_base_url = 'https://www.esportsvikings.com';
		$this->_img_base_url = $img_base_url;
    }    
    function getInnerHTML(&$node) {
		 ## if html parameter not specified, return the current contents of $node
		$doc = new \DOMDocument('1.0', 'utf-8');
		foreach ($node->childNodes as $child)
		$doc->appendChild($doc->importNode($child, true));

		return $doc->saveHTML();
	}
	private function getHtml($nodes) {
	  $result = '';
	  foreach ($nodes as $node) {
	    $result .= $node->ownerDocument->saveHtml($node);
	  }
	  return $result;
	}

    public function getPostInfo(){
    	
		$path = parse_url($this->_url, PHP_URL_PATH);		
		$path_size = count(explode('/', $path));

	    $html = $this->curl($this->_url);
	    $dom = new \DOMDocument('1.0', 'utf-8');
	    libxml_use_internal_errors(true);//remove warning from loadHTML()
	    $dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $html);
	    $html = $dom->saveHTML();          
	    // title and metas
	    $nodes = $dom->getElementsByTagName('title');   	
	    $this->_post_title = trim($nodes->item(0)->nodeValue);    

	    $metas = $dom->getElementsByTagName('meta');
	    $description = '';
	    for ($i = 0; $i < $metas->length; $i++){
		    $meta = $metas->item($i);
		    if($meta->getAttribute('name') == 'description')
		        $description = $meta->getAttribute('content');	    
		}	
		$this->_meta_description = $description;
		//div content
		
		$xpath = new \DOMXPath($dom);    
		$body = $xpath->query($this->_query)->item(0);
	   	
		$children = $body->childNodes; 
		foreach ($children as $child) { 
		    $this->_post_content .= $child->ownerDocument->saveHTML( $child ); 
		}
	    
	    //$this->_post_content =  $innerHTML;
	    //images
	    $images = $xpath->query($this->_query.'//img');
	    for ($i = 0; $i < $images->length; $i++){
		    $node = $images->item($i);
		    
		    if ( (strpos($node->getAttribute('data-src'), 'base64,') !== false) || empty($node->getAttribute('data-src')) ) {
				$myLink = $node->getAttribute('src');
			} else {
				$myLink = $node->getAttribute('data-src');
			}		
		    
		    if(substr($myLink,0,8) == 'https://') {
		    	$pathinfo = pathinfo($myLink);
	        	$this->_post_images[] = json_encode(array('url'=>$myLink, 'name'=>$pathinfo['filename'].'.'.$pathinfo['extension']));
		    } else {
		    	if( substr($myLink,0,1) == '/' ){
		    		$pathinfo = pathinfo($this->_img_base_url.$myLink);
		    		$this->_post_images[] = json_encode(array('url'=>$myLink, 'name'=>$pathinfo['filename'].'.'.$pathinfo['extension']));
		    	}else{		    		
		    		$pathinfo = pathinfo($this->_img_base_url.'/'.$myLink);
		    		$this->_post_images[] = json_encode(array('url'=>$myLink, 'name'=>$pathinfo['filename'].'.'.$pathinfo['extension']));
		    	}		        
		    }
		}	
		$anchors = $xpath->query($this->_query.'//a');	    

	    for ($i = 0; $i < $anchors->length; $i++){
		    $node = $anchors->item($i);
		    $myLink = $node->getAttribute('href');
			if( !empty($myLink) ){

			    if(substr($myLink,0,8) == 'https://') {
		        	$this->_post_anchors[] = $myLink;
			    } else {
			    	if( substr($myLink,0,1) == '/' ){
			    		$this->_post_anchors[] = $this->_base_url.$myLink;	
			    	}else{
			    		$this->_post_anchors[] = $this->_base_url.'/'.$myLink;
			    	}
			    }
		    }   
		}
	    $date = date('Y-m-d');	    
	    $this->_post_date = date('Y-m-d H:i:s', strtotime($date));		    
	    if( $path_size > 3 ){
		    $date = $xpath->query('//span[@class="date"]');
		    $date = $date->item(0)->nodeValue;    		    
		    $this->_post_date = date('Y-m-d H:i:s', strtotime($date));
	    }
	    
	}
	public function getMetaDescription(){
		return $this->_meta_description;
	}
	public function getPostTitle(){
		return $this->_post_title;
	}

	public function getPostImages(){
		return $this->_post_images;
	}
	public function getPostAnchors(){
		return $this->_post_anchors;
	}
	public function getPostContent(){
		return $this->_post_content;
	}  	
  	private function curl($url){    
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    // Blindly accept the certificate
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    // decode response
	    curl_setopt($ch, CURLOPT_ENCODING, true);
	    $output = curl_exec($ch);
	    curl_close($ch);
	    return $output;
	}
	
}