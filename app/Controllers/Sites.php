<?php

namespace App\Controllers;

use App\Libraries\Post;

class Sites extends BaseController
{
    public static $arrayPages = array("milord");

    public function index($idsite)
    {
        $sitesModel = new \App\Models\SitesModel();
        $pagesModel = new \App\Models\PagesModel();
        $site = $sitesModel->find($idsite);
        $pages = $pagesModel->where('idsite', $site->idsite)->where('act', 1)->findAll();
        //echo $sites;
        $dataHeader["title"] = $site->domain;
        $dataHeader["detail"] = "<div class='mt-4 mb-2 text-start'><strong>Username N.nu:</strong> ".$site->nick;
        $dataHeader["detail"] .= "<br><strong>Searching content by:</strong><br>";
        $dataHeader["detail"] .= "<ul>".
                                    "<li>Tag: ".$site->tagcontent."</li>".
                                    "<li>Class: ".$site->classcontent."</li>".
                                    "<li>Id: ".$site->idcontent."</li>"
                                ."</ul>";
        $dataHeader["detail"] .= "</div>";

        $data["pages"] = $pages;
        $data["site"] = $site;
        return view('templates/header', $dataHeader)
        .view('listPages',$data)
        .view('templates/footer');
    }

    public function saveSite()
    {
        $sitesModel = new \App\Models\SitesModel();
        $idsite = $this->request->getVar('idsite');
        $data = [
            'domain' => $this->request->getVar('domain'),
            'nick'    => $this->request->getVar('nick'),
            'tagcontent'    => $this->request->getVar('tagcontent'),
            'classcontent'    => $this->request->getVar('classcontent'),
            'idcontent'    => $this->request->getVar('idcontent'),
            'title' => $this->request->getVar('title'),
        ];
        $sitesModel->update($idsite, $data);
        return $this->response->redirect(site_url('/site/'.$idsite));
    }

    public function viewPage($idpage)
    {
        $pagesModel = new \App\Models\PagesModel();
        $sitesModel = new \App\Models\SitesModel();
        $imagesModel = new \App\Models\ImagesModel();
        $page = $pagesModel->find($idpage);
        if($page){
            
            $site = $sitesModel->find($page->idsite);
            $dataHeader["title"] = $page->title;
            $dataHeader["detail"] = $page->descriptionpage;
            $dataHeader["detail"] .= "<br><div class='mt-4 mb-2 text-start'><strong>URL page:</strong> ".$site->domain.$page->path;
            $dataHeader["detail"] .= "<br><strong>Site:</strong> ".$site->domain;
            $dataHeader["detail"] .= "<br><strong>Username N.nu:</strong> ".$site->nick;
            $dataHeader["detail"] .= "<br><strong>Searching content by:</strong><br>";
            $dataHeader["detail"] .= "<ul>".
                                        "<li>Tag: ".$site->tagcontent."</li>".
                                        "<li>Class: ".$site->classcontent."</li>".
                                        "<li>Id: ".$site->idcontent."</li>"
                                    ."</ul>";
            $dataHeader["detail"] .= "</div>";
        }
        else{
            return $this->response->redirect(site_url('/'));    
        }

        $listImages = $imagesModel->imagesByPage($page->idpage);

        $data["page"] = $page;
        $data["site"] = $site;
        $data["listImages"] = $listImages;
        return view('templates/header', $dataHeader)
        .view('pageView',$data)
        .view('templates/footer');
    }

    public function setXML($idsite)
    {
        $sitesModel = new \App\Models\SitesModel();
        $site = $sitesModel->find($idsite);
        $dataHeader["title"] = $site->domain;
        $dataHeader["detail"] = "<div class='mt-4 mb-2 text-start'><strong>Username N.nu:</strong> ".$site->nick;
        $dataHeader["detail"] .= "<br><strong>Searching content by:</strong><br>";
        $dataHeader["detail"] .= "<ul>".
                                    "<li>Tag: ".$site->tagcontent."</li>".
                                    "<li>Class: ".$site->classcontent."</li>".
                                    "<li>Id: ".$site->idcontent."</li>"
                                ."</ul>";
        $dataHeader["detail"] .= "</div>";
        
        $data["site"] = $site;

        return view('templates/header', $dataHeader)
        .view('formXML',$data)
        .view('templates/footer');

    }

    public function scanXML($idsite)
    {
        $idsite = $this->request->getVar('idsite');
        $sitemapXml = $this->request->getVar('sitemapXml');
        $sitesModel = new \App\Models\SitesModel();
        $site = $sitesModel->find($idsite);
        $dataHeader["title"] = $site->domain;
        $dataHeader["detail"] = "<div class='mt-4 mb-2 text-start'><strong>Username N.nu:</strong> ".$site->nick;
        $dataHeader["detail"] .= "<br><strong>Searching content by:</strong><br>";
        $dataHeader["detail"] .= "<ul>".
                                    "<li>Tag: ".$site->tagcontent."</li>".
                                    "<li>Class: ".$site->classcontent."</li>".
                                    "<li>Id: ".$site->idcontent."</li>"
                                ."</ul>";
        $dataHeader["detail"] .= "</div>";
        
        $objectXml = simplexml_load_string($sitemapXml);
        if ($objectXml === FALSE) {
            $data["error"] = "There were errors parsing the XML file.";
        }
        else{
            $data = [
                'xmlpages' => $sitemapXml,
            ];
            $sitesModel->update($idsite, $data);
        }

        $pagesModel = new \App\Models\PagesModel();
        $savedPages = $pagesModel->where('idsite', $idsite)->where('act', 1)->findAll();
        $listPages = array();
        $i = 0;
        $saveds = 0;
        $new = 0;
        foreach($objectXml->url as $url){
            $isSet = false;
            for($a = 0; $a < sizeof($savedPages); $a++ )
            {
                if( ($site->domain . $savedPages[$a]->path) == $url->loc )
                {
                    $isSet = true;
                    $a = sizeof($savedPages);
                    
                }
            }
            if($isSet)
            {
                $u = new \stdClass;
                $u->loc = $url->loc;
                $u->lastmod = $url->lastmod;
                $u->priority = $url->priority;
                $u->isSaved = true;
                array_push($listPages, $u);
                $saveds ++;
            }
            else
            {
                $u = new \stdClass;
                $u->loc = $url->loc;
                $u->lastmod = $url->lastmod;
                $u->priority = $url->priority;
                $u->isSaved = false;
                array_push($listPages, $u);
                $new++;
            }

        }

        $data["site"] = $site;
        $data['sitemapXml'] = $listPages;
        return view('templates/header', $dataHeader)
        .view('reviewXML',$data)
        .view('templates/footer');
    }

    public function savePage($idsite)
    {
        $sitesModel = new \App\Models\SitesModel();
        $site = $sitesModel->find($idsite);
        $loc = $this->request->getVar('loc');
        //+++++++++++++ AQUI COLOCAR EL PROCESO DE html SCRIPLING
        $format = "";
        if($site->classcontent)
        {$format = '//'.$site->tagcontent.'[@class="'.$site->classcontent.'"]'; }
        if($site->idcontent)
        {$format = '//'.$site->tagcontent.'[@id="'.$site.idcontent.'"]'; }

        $post = new Post($loc, $format, 'https://cdn.esportsvikings.com', 'https://cdn.esportsvikings.com');
        $post->getPostInfo();
        $title = $post->getPostTitle();
        $descriptionpage = $post->getMetaDescription();
        $content = $post->getPostContent();
        $images = $post->getPostImages();
        //-----------------------------------------------------------
        $pagesModel = new \App\Models\PagesModel();
        $foundPage = $pagesModel->where('path', trim(str_replace($site->domain,'',$loc)))->findAll();
        if(sizeOf($foundPage)==0){
            $data = [
                'idsite' => $site->idsite,
                'title' => $title,
                'descriptionpage'    => $descriptionpage,
                'path'    => trim(str_replace($site->domain,'',$loc)),
                'content'    => $content,
                'lastmod'    => $this->request->getVar('lastmod'),
                'priority'    => $this->request->getVar('priority'),
                'act' => 1
            ];
            $pagesModel->insert($data);
            $idPage = $pagesModel->getInsertID();
        }
        else{
            $idPage = $foundPage->idpage;
        }
        
        //++++++++++++ Saving images
        $imagesModel = new \App\Models\ImagesModel();
        $pageImageModel = new \App\Models\PageImageModel();
        foreach($images as $imageString)
        {
            $image = json_decode($imageString); //++++ encode to json, so we have to decode
            $foundImage = $imagesModel->where('url', trim($image->url))->findAll();
            if(sizeOf($foundImage)>0){
                //+++++ The image is already saved but Just To indicate how much is used the image. So It's registered in 'page_image'
                $data = [
                    'idpage' => $idPage,
                    'idimage' => $foundImage[0]->idimage
                ];
                $pageImageModel->insert($data);
            }
            else{
                $data = [
                    'name'=> $image->name,
                    'url'=> $image->url,
                    'urlnnu'=> '',
                    'act'=> 1,
                ];
                $imagesModel->insert($data);
                $idImage = $imagesModel->getInsertID();
                $data = [
                    'idpage' => $idPage,
                    'idimage' => $idImage
                ];
                $pageImageModel->insert($data);
            }
        }
        $response = [
            'success' => true,
            'message' => "Se guardo correctamente"
        ];
        return $this->response->setJSON($response);
    }

    public function downloadXml($idSite)
    {
        $pagesModel = new \App\Models\PagesModel();
        $sitesModel = new \App\Models\SitesModel();
        $imagesModel = new \App\Models\ImagesModel();
        $site = $sitesModel->find($idSite);
        $pages = $pagesModel->where('idsite', $site->idsite)->where('act', 1)->findAll();

        $dom = new \DOMDocument();
		$dom->encoding = 'utf-8';
		$dom->xmlVersion = '1.0';
		$dom->formatOutput = true;
	    $xml_file_name = 'export-site-'.$site->nick.'.xml';

        $rss = $dom->createElement('rss');
        $attr_rss = new DOMAttr('version', '2.0');
        $rss->setAttributeNode($attr_rss);
        $channel = $dom->createElement('channel');
        $titleChild = $dom->createElement('title', $site->title);
        $channel->appendChild($titleChild);
        $linkChild = $dom->createElement('link', $site->domain);
        $channel->appendChild($linkChild);
        $descriptionChild = $dom->createElement('description', '');
        $channel->appendChild($descriptionChild);
        $pubDateChild = $dom->createElement('pubDate', '');
        $channel->appendChild($pubDateChild);
        $languageChild = $dom->createElement('language', '');
        $channel->appendChild($languageChild);

        $item = $dom->createElement('item');
        $titleItemChild = $dom->createElement('title', 'Default Post');
        $item->appendChild($titleItemChild);
        $linkItemChild = $dom->createElement('link', $site->domain.'default-post');
        $item->appendChild($linkItemChild);
        $pubDateItemChild = $dom->createElement('pubDate', 'Fri, 03 Dec 2021 02:26:32 +0000');
        $item->appendChild($pubDateItemChild);
        $creatorItemChild = $dom->createElement('dc:creator', '<![CDATA[admin]]>');
        $item->appendChild($creatorItemChild);
        $descriptionItemChild = $dom->createElement('description', 'Description of content of defaul post');
        $item->appendChild($descriptionItemChild);

        $contentItemChild = $dom->createElement('content:encoded', '<![CDATA[MAIN content of defaul post]]');
        $item->appendChild($contentItemChild);

        $typeItemChild = $dom->createElement('wp:post_type', '<![CDATA[post]]');
        $item->appendChild($typeItemChild);

        $channel->appendChild($item);

        $rss->appendChild($channel);
    }

    public function testJson()
    {
        $url = 'https://www.esportsvikings.com/csgo/tournaments/esl-pro-league-season-12-north-america';
        
        $post = new Post($url, '//div[@class="main"]', 'https://cdn.esportsvikings.com', 'https://cdn.esportsvikings.com');
        $post->getPostInfo();
        // echo '<textarea>';
        // print_r($post->getPostImages());
        // echo '</textarea>';
        // echo '<textarea>';
        // echo $post->getPostTitle();
        // echo '</textarea>';
        $data = [
            'success' => true,
            'message'      => 123,
            'title' => $post->getPostTitle(),
            'description' => $post->getMetaDescription(),
            'content' => $post->getPostContent(),
        ];
        
        return $this->response->setJSON($data);
    }
   
}
