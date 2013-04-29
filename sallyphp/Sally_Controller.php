<?php

class Sally_Controller
{
  public $view;

  public function __construct()
  {
    $this->view = Sally_View::getInstance();
    $this->request = Sally_Request::getInstance();
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

  public function forward($action = 'index', $controller = null, $module = null)
  {
    $sally = Sally::getInstance();

    if ($module == null) {
      $this->request->setModule($this->request->getModule());
    } else {
      $this->request->setModule($module);
    }

    if ($controller == null) {
      $this->request->setController($this->request->getController());
    } else {
      $this->request->setController($controller);
    }

    $this->request->setAction($action);
    $sally->enableForward();
  }
}