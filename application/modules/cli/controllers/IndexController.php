<?php

class IndexController extends Sally_Controller
{
  public function init()
  {
    //$this->db = Sally_Db::getConnection();
  }

  public function index()
  {
    echo 'utilisation depuis ligne de commande...';
  }
}