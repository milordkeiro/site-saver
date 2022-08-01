<?php

namespace App\Controllers;

class GeneratorXml extends BaseController
{
    public function index($idSite)
    {
        $pagesModel = new \App\Models\PagesModel();
        $sitesModel = new \App\Models\SitesModel();
        $imagesModel = new \App\Models\ImagesModel();
        $site = $sitesModel->find($idSite);
        $pages = $pagesModel->where('idsite', $site->idsite)->where('act', 1)->findAll();

        $itemsXml = '';
        foreach($pages as $page){
        $page->content = str_replace(array("\r", "\n"), "", $page->content);
        $listImage = $imagesModel->imagesByPage($page->idpage);
            //$formatedImages = array();
            
            $index = 0;
            foreach($listImage as $image)
            {
                $counter = 0;
                $urlImageNoParam = strtok($image->url, '?');
                $page->content = str_replace(array($image->url), $urlImageNoParam, $page->content); //+++ We have url image without parameters into content
                $page->content = preg_replace_callback("#".$urlImageNoParam."#", function ($m) use (&$counter, &$index) {

                   if($counter==0){
                    $counter++;
                    return "MILORD".$index."MILORD";
                   }
                    
                   return $m[0];
          
                }, $page->content);

                $index++;
            }

            $index = 0;
            $defaultTransparentImg = 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=';
            foreach($listImage as $image)
            {
                if(strpos($image->url, '/assets/') !== false)
                {
                    $image->url = str_replace('/assets/', "https://www.esportsvikings.com/assets/", $image->url);
                }

                $file_headers = @get_headers($image->url);
                if($file_headers[0] == 'HTTP/1.1 404 Not Found'){
                    $image->url = $defaultTransparentImg;
                } 
                else 
                {
                    if ($file_headers[0] == 'HTTP/1.1 302 Found' && $file_headers[7] == 'HTTP/1.1 404 Not Found'){
                        $image->url = $defaultTransparentImg;
                    } 
                    else 
                    {
                        // if(is_array(getimagesize($image->url))) {
                        //     //echo "The file exists";
                        // } else {
                        //     //echo "The file does not exist";
                        //     $image->url = $defaultTransparentImg;
                        // }
                    }
                }
                
                
                //++++ replacing https to http
                $random = rand ( 10000 , 99999 );
                if($image->url != $defaultTransparentImg)
                {
                $urlImageNoParam = strtok($image->url, '?');
                $splitImage = explode('.',$urlImageNoParam);
                $extention = $splitImage[sizeof($splitImage)-1];
                //echo $extention;
                $forceHttp = "http://clients.todaysweb.com/curl.php?url=".$urlImageNoParam."?_imgcomp".$index.".".$extention;
                $page->content = str_replace("MILORD".$index."MILORD", $forceHttp, $page->content);
                $page->content = str_replace('src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="'.$forceHttp.'"', 'src="'.$forceHttp.'"', $page->content);
                }
                else{
                    $page->content = str_replace("MILORD".$index."MILORD", $defaultTransparentImg, $page->content);    
                }
                $index++;
            }
        

        $itemsXml .= '<item>
        <title><![CDATA['.$page->title.']]></title>
        <link>'.$page->path.'</link>
        <pubDate>'.$page->lastmod.'</pubDate>
        <dc:creator><![CDATA[admin]]></dc:creator>
        <guid isPermaLink="false">'.$site->domain.'?page_id='.$page->idpage.'</guid>
        <description><![CDATA['.$page->descriptionpage.']]></description>
        <content:encoded><![CDATA['.$page->content.']]></content:encoded>
        <excerpt:encoded><![CDATA[]]></excerpt:encoded>
        <wp:post_id>'.$page->idpage.'</wp:post_id>
        <wp:post_date><![CDATA['.$page->lastmod.']]></wp:post_date>
        <wp:post_date_gmt><![CDATA['.$page->lastmod.']]></wp:post_date_gmt>
        <wp:post_modified><![CDATA['.$page->lastmod.']]></wp:post_modified>
        <wp:post_modified_gmt><![CDATA['.$page->lastmod.']]></wp:post_modified_gmt>
        <wp:comment_status><![CDATA[closed]]></wp:comment_status>
        <wp:ping_status><![CDATA[open]]></wp:ping_status>
        <wp:post_name><![CDATA[sample-page]]></wp:post_name>
        <wp:status><![CDATA[publish]]></wp:status>
        <wp:post_parent>0</wp:post_parent>
        <wp:menu_order>0</wp:menu_order>
        <wp:post_type><![CDATA[page]]></wp:post_type>
        <wp:post_password><![CDATA[]]></wp:post_password>
        <wp:is_sticky>0</wp:is_sticky>
                                                        <wp:postmeta>
        <wp:meta_key><![CDATA[_wp_page_template]]></wp:meta_key>
        <wp:meta_value><![CDATA[default]]></wp:meta_value>
        </wp:postmeta>
                            <wp:postmeta>
        <wp:meta_key><![CDATA[_edit_last]]></wp:meta_key>
        <wp:meta_value><![CDATA[1]]></wp:meta_value>
        </wp:postmeta>
                            <wp:postmeta>
        <wp:meta_key><![CDATA[zakra_layout]]></wp:meta_key>
        <wp:meta_value><![CDATA[tg-site-layout--customizer]]></wp:meta_value>
        </wp:postmeta>
                            <wp:postmeta>
        <wp:meta_key><![CDATA[zakra_remove_content_margin]]></wp:meta_key>
        <wp:meta_value><![CDATA[0]]></wp:meta_value>
        </wp:postmeta>
                            <wp:postmeta>
        <wp:meta_key><![CDATA[zakra_transparent_header]]></wp:meta_key>
        <wp:meta_value><![CDATA[customizer]]></wp:meta_value>
        </wp:postmeta>
                            <wp:postmeta>
        <wp:meta_key><![CDATA[zakra_menu_item_color]]></wp:meta_key>
        <wp:meta_value><![CDATA[]]></wp:meta_value>
        </wp:postmeta>
                            <wp:postmeta>
        <wp:meta_key><![CDATA[zakra_menu_item_hover_color]]></wp:meta_key>
        <wp:meta_value><![CDATA[]]></wp:meta_value>
        </wp:postmeta>
                            <wp:postmeta>
        <wp:meta_key><![CDATA[zakra_page_header]]></wp:meta_key>
        <wp:meta_value><![CDATA[1]]></wp:meta_value>
        </wp:postmeta>
                            <wp:postmeta>
        <wp:meta_key><![CDATA[zakra_logo]]></wp:meta_key>
        <wp:meta_value><![CDATA[0]]></wp:meta_value>
        </wp:postmeta>
                            </item>';
        
        }
        $xmlText = '<?xml version="1.0" encoding="UTF-8" ?>
        <rss version="2.0"
            xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/"
            xmlns:content="http://purl.org/rss/1.0/modules/content/"
            xmlns:wfw="http://wellformedweb.org/CommentAPI/"
            xmlns:dc="http://purl.org/dc/elements/1.1/"
            xmlns:wp="http://wordpress.org/export/1.2/"
        >
        <channel>
            <title>'.$site->title.'</title>
            <link>'.$site->domain.'</link>
            <description></description>
            <pubDate>Thu, 14 Jul 2022 15:10:44 +0000</pubDate>
            <language>en</language>
            <wp:wxr_version>1.2</wp:wxr_version>
            <wp:base_site_url>'.$site->domain.'</wp:base_site_url>
            <wp:base_blog_url>'.$site->domain.'</wp:base_blog_url>
        
                <wp:author><wp:author_id>1</wp:author_id>
                <wp:author_login><![CDATA[admin]]></wp:author_login>
                <wp:author_email><![CDATA[info@todaysweb.com]]></wp:author_email>
                <wp:author_display_name><![CDATA[admin]]></wp:author_display_name>
                <wp:author_first_name><![CDATA[]]></wp:author_first_name>
                <wp:author_last_name><![CDATA[]]></wp:author_last_name>
                </wp:author>
        
                <wp:category>
                <wp:term_id>1</wp:term_id>
                <wp:category_nicename><![CDATA[uncategorized]]></wp:category_nicename>
                <wp:category_parent><![CDATA[]]></wp:category_parent>
                <wp:cat_name><![CDATA[Uncategorized]]></wp:cat_name>
            </wp:category>
                        <wp:term>
                <wp:term_id>2</wp:term_id>
                <wp:term_taxonomy><![CDATA[nav_menu]]></wp:term_taxonomy>
                <wp:term_slug><![CDATA[menu-cabecera]]></wp:term_slug>
                <wp:term_parent><![CDATA[]]></wp:term_parent>
                <wp:term_name><![CDATA[Menu Cabecera]]></wp:term_name>
            </wp:term>
                <wp:term>
                <wp:term_id>1</wp:term_id>
                <wp:term_taxonomy><![CDATA[category]]></wp:term_taxonomy>
                <wp:term_slug><![CDATA[uncategorized]]></wp:term_slug>
                <wp:term_parent><![CDATA[]]></wp:term_parent>
                <wp:term_name><![CDATA[Uncategorized]]></wp:term_name>
            </wp:term>
                <wp:term>
                <wp:term_id>3</wp:term_id>
                <wp:term_taxonomy><![CDATA[wp_theme]]></wp:term_taxonomy>
                <wp:term_slug><![CDATA[zakra]]></wp:term_slug>
                <wp:term_parent><![CDATA[]]></wp:term_parent>
                <wp:term_name><![CDATA[zakra]]></wp:term_name>
            </wp:term>
                    <wp:term><wp:term_id>2</wp:term_id><wp:term_taxonomy>nav_menu</wp:term_taxonomy><wp:term_slug><![CDATA[menu-cabecera]]></wp:term_slug><wp:term_name><![CDATA[Menu Cabecera]]></wp:term_name>
        </wp:term>
        
            <generator>https://wordpress.org/?v=6.0.1</generator>
        
        
                <item>
                <title><![CDATA[Default Post]]></title>
                <link>default-post/</link>
                <pubDate>Fri, 03 Dec 2021 02:26:32 +0000</pubDate>
                <dc:creator><![CDATA[admin]]></dc:creator>
                <guid isPermaLink="false">'.$site->domain.'?p=1</guid>
                <description></description>
                <content:encoded><![CDATA[+++++ CONTENT OF DEFAULT POST ++++++]]></content:encoded>
                <excerpt:encoded><![CDATA[]]></excerpt:encoded>
                <wp:post_id>1</wp:post_id>
                <wp:post_date><![CDATA[2021-12-03 02:26:32]]></wp:post_date>
                <wp:post_date_gmt><![CDATA[2021-12-03 02:26:32]]></wp:post_date_gmt>
                <wp:post_modified><![CDATA[2022-07-14 14:13:31]]></wp:post_modified>
                <wp:post_modified_gmt><![CDATA[2022-07-14 14:13:31]]></wp:post_modified_gmt>
                <wp:comment_status><![CDATA[open]]></wp:comment_status>
                <wp:ping_status><![CDATA[open]]></wp:ping_status>
                <wp:post_name><![CDATA[hello-world]]></wp:post_name>
                <wp:status><![CDATA[publish]]></wp:status>
                <wp:post_parent>0</wp:post_parent>
                <wp:menu_order>0</wp:menu_order>
                <wp:post_type><![CDATA[post]]></wp:post_type>
                <wp:post_password><![CDATA[]]></wp:post_password>
                <wp:is_sticky>0</wp:is_sticky>
                                                <category domain="category" nicename="uncategorized"><![CDATA[Uncategorized]]></category>
                                <wp:postmeta>
                <wp:meta_key><![CDATA[_pingme]]></wp:meta_key>
                <wp:meta_value><![CDATA[1]]></wp:meta_value>
                </wp:postmeta>
                                    <wp:postmeta>
                <wp:meta_key><![CDATA[_edit_last]]></wp:meta_key>
                <wp:meta_value><![CDATA[1]]></wp:meta_value>
                </wp:postmeta>
                                    <wp:postmeta>
                <wp:meta_key><![CDATA[_encloseme]]></wp:meta_key>
                <wp:meta_value><![CDATA[1]]></wp:meta_value>
                </wp:postmeta>
                                    <wp:postmeta>
                <wp:meta_key><![CDATA[zakra_layout]]></wp:meta_key>
                <wp:meta_value><![CDATA[tg-site-layout--customizer]]></wp:meta_value>
                </wp:postmeta>
                                    <wp:postmeta>
                <wp:meta_key><![CDATA[zakra_remove_content_margin]]></wp:meta_key>
                <wp:meta_value><![CDATA[0]]></wp:meta_value>
                </wp:postmeta>
                                    <wp:postmeta>
                <wp:meta_key><![CDATA[zakra_transparent_header]]></wp:meta_key>
                <wp:meta_value><![CDATA[customizer]]></wp:meta_value>
                </wp:postmeta>
                                    <wp:postmeta>
                <wp:meta_key><![CDATA[zakra_menu_item_color]]></wp:meta_key>
                <wp:meta_value><![CDATA[]]></wp:meta_value>
                </wp:postmeta>
                                    <wp:postmeta>
                <wp:meta_key><![CDATA[zakra_menu_item_hover_color]]></wp:meta_key>
                <wp:meta_value><![CDATA[]]></wp:meta_value>
                </wp:postmeta>
                                    <wp:postmeta>
                <wp:meta_key><![CDATA[zakra_page_header]]></wp:meta_key>
                <wp:meta_value><![CDATA[1]]></wp:meta_value>
                </wp:postmeta>
                                    <wp:postmeta>
                <wp:meta_key><![CDATA[zakra_logo]]></wp:meta_key>
                <wp:meta_value><![CDATA[0]]></wp:meta_value>
                </wp:postmeta>
                                    <wp:comment>
                    <wp:comment_id>1</wp:comment_id>
                    <wp:comment_author><![CDATA[A WordPress Commenter]]></wp:comment_author>
                    <wp:comment_author_email><![CDATA[wapuu@wordpress.example]]></wp:comment_author_email>
                    <wp:comment_author_url>https://wordpress.org/</wp:comment_author_url>
                    <wp:comment_author_IP><![CDATA[]]></wp:comment_author_IP>
                    <wp:comment_date><![CDATA[2021-12-03 02:26:32]]></wp:comment_date>
                    <wp:comment_date_gmt><![CDATA[2021-12-03 02:26:32]]></wp:comment_date_gmt>
                    <wp:comment_content><![CDATA[Hi, this is a comment.
        To get started with moderating, editing, and deleting comments, please visit the Comments screen in the dashboard.
        Commenter avatars come from <a href="https://gravatar.com">Gravatar</a>.]]></wp:comment_content>
                    <wp:comment_approved><![CDATA[1]]></wp:comment_approved>
                    <wp:comment_type><![CDATA[comment]]></wp:comment_type>
                    <wp:comment_parent>0</wp:comment_parent>
                    <wp:comment_user_id>0</wp:comment_user_id>
                                    </wp:comment>
                            <wp:comment>
                    <wp:comment_id>2</wp:comment_id>
                    <wp:comment_author><![CDATA[admin]]></wp:comment_author>
                    <wp:comment_author_email><![CDATA[info@todaysweb.com]]></wp:comment_author_email>
                    <wp:comment_author_url>'.$site->domain.'</wp:comment_author_url>
                    <wp:comment_author_IP><![CDATA[181.114.71.126]]></wp:comment_author_IP>
                    <wp:comment_date><![CDATA[2022-01-31 13:43:28]]></wp:comment_date>
                    <wp:comment_date_gmt><![CDATA[2022-01-31 13:43:28]]></wp:comment_date_gmt>
                    <wp:comment_content><![CDATA[Hola Jannet]]></wp:comment_content>
                    <wp:comment_approved><![CDATA[1]]></wp:comment_approved>
                    <wp:comment_type><![CDATA[comment]]></wp:comment_type>
                    <wp:comment_parent>0</wp:comment_parent>
                    <wp:comment_user_id>1</wp:comment_user_id>
                                    </wp:comment>
                            </item>
                    '.$itemsXml.'
                        </channel>
        </rss>
            
            ';

        $name = 'export-sitesaver.WordPress.2022-07-14.xml';
        return $this->response->download($name, $xmlText);

    }

    
}
