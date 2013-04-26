<?php

class Sally_Controller
{
  public $view;

  public function __construct()
  {
    $this->view = Sally_View::getInstance();
  }

  public function model($name)
  {
    $model = Sally_Model::getInstance();
    return $model->load($name);
  }

  public function helper($name)
  {
    $helper = Sally_Helper::getInstance();
    return $helper->load($name);
  }

  public function redirect($url)
  {
    header('Location: ' . $url);
    exit;
  }
}