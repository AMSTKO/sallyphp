<?php
class IndexController extends Sally_Controller
{
  public function init()
  {
    
  }

  public function index()
  {
    $lol = $this->model('/Lol');
    echo $lol->getEmail() . ' admin<br>' . $this->request->controller;
    $this->view->lol = 'haha';
  }

  public function lol()
  {

  }
}