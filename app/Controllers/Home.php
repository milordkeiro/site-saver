<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $sitesModel = new \App\Models\SitesModel();
        $sites = $sitesModel->where('act', 1)->findAll();
        //echo $sites;
        $dataHeader["title"] = "List of Sites";
        $dataHeader["detail"] = "";

        $data["sites"] = $sites;
        return view('templates/header', $dataHeader)
        .view('listSites',$data)
        .view('templates/footer');
    }

    public function page1()
    {
        return view('templates/header')
        .view('page1_view')
        .view('templates/footer');
    }
    public function anyPage($uri='')
    {
        $data["uri"] = $uri;
        return view('templates/header')
        .view('anypage_view',$data)
        .view('templates/footer');
    }
}
