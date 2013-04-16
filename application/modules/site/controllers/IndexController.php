<?php
class IndexController extends Sally_Controller
{
  public function init()
  {
    echo 'etape1<br>';
  }

  public function index()
  {
    $lol = $this->model('/lol');
    echo $lol->getEmail() . ' site<br>' . $this->request->controller;
  }
}