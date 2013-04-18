<?php
class IndexController extends Sally_Controller
{
  public function init()
  {
    
  }

  public function index()
  {
    $lol = $this->model('/lol');
  }
}