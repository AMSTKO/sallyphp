<?php

class IndexController extends Sally_Controller
{
  public function init()
  {
    $this->db = Sally_Db::getConnection();
  }

  private function curl($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $out = curl_exec($curl);
    curl_close($curl);
    return json_decode($out, true);
  }

  public function index()
  {
    // action...
  }
}