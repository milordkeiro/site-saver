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
    function __construct($url, $query, $img_base_url='https://cdn.urheilu.com') {
        //require_once 'dababase.class.php';
        $this->_url = $url;
        $this->_query = $query;
        $this->_base_url = 'https://www.urheilu.com';
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
		$url_path = explode('/', $path);
		$path_size = count($url_path);
		
	    $html = $this->curl($this->_url);

	    @$dom = new \DOMDocument('1.0', 'utf-8');
	    libxml_use_internal_errors(true);//remove warning from loadHTML()
	    @$dom->loadHTML($html);
	    $html = @$dom->saveHTML();          

	    $anchors = $dom->getElementsByTagName("a");
		for ($i = $anchors->length - 1; $i >= 0; $i --) {
			$node = $anchors->item($i);
			if( strpos($node->getAttribute('href'), 'cdn-cgi/l/email-protection') !== false ){    
			    if( $node->getAttribute('title') == "'Author email'" ){			    	
	            	$encStr = 	$node->getAttribute('href');
	            	$encodedString = substr($encStr, strpos($encStr, "#") + 1 );             	
	            	$email = $this->cfDecodeEmail($encodedString);
	            	$nodeA = $dom->createElement("a", $email);
	            	$nodeA->setAttribute('_ngcontent-sc46', "");
	            	$nodeA->setAttribute('rel', "nofollow");
	            	$nodeA->setAttribute('title', "'Author email'");
	            	$nodeA->setAttribute('href', "mailto:".$email);
	            	$node->parentNode->replaceChild($nodeA, $node);            	
	            }else{	            	
	            	$encodedString = $node->getAttribute('data-cfemail');
	            	$email = $this->cfDecodeEmail($encodedString);
	            	$node->parentNode->replaceChild($dom->createTextNode($email), $node);            	
	            }
            }
		}	
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
        $numb = 3;        
	    if( $url_path[1]=='se' || $url_path[1]=='fi' || $url_path[1]=='jp' ){
	    	$numb = 4;
	    }     

        if( $path_size > $numb ){
            $date = $xpath->query('//span[@class="date"]');
            $date = $date->item(0)->nodeValue;                        
            $this->_post_date = date('Y-m-d H:i:s', strtotime($date));
        }else{
            $style_images = array();
            if( $path_size === $numb-1 ){
                    $node = $xpath->query($this->_query.'//div[@class="summary-img"]');
                    if( $node->length != 0){
                            $style = $node->item(0)->getAttribute('style');
                            $style_images = $this->extract_css_urls($style);
                            $pathinfo = pathinfo($style_images[0]);
                        $this->_post_images[] = json_encode(array('url'=>$style_images[0], 'name'=>$pathinfo['filename'].'.'.$pathinfo['extension']));
                }
            }
        
        }
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
	    
	    
	}
	private function cfDecodeEmail($encodedString) {
	    $k = hexdec(substr($encodedString, 0, 2));
	    for ($i = 2, $email = ''; $i < strlen($encodedString) - 1; $i += 2) {
	       $email .= chr(hexdec(substr($encodedString, $i, 2)) ^ $k);
	    }
	    return $email;
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
	// Extract URLs from CSS text.
    private function extract_css_urls($text){
            $urls['import'] = array();
            preg_match_all('/url\(([\s])?([\"|\'])?(.*?)([\"|\'])?([\s])?\)/i', $text, $matches, PREG_PATTERN_ORDER);
            if($matches){
                    foreach ( $matches as $match ){
                            if ( !empty($match))
                                    $urls['import'][] = preg_replace( '/\\\\(.)/u', '\\1', $match );
                    }
            }
            if(isset($urls['import'][3]))
                    return $urls['import'][3];
            else
                    $urls['import'];
    }
	
}