<?php

class MyTrafficker extends Sally_Trafficker_Abstract
{
  function __construct()
  {
    $this->layout = Sally_Layout::getInstance();
    $this->view = Sally_View::getInstance();
    $this->request = Sally_Request::getInstance();
  }

  function preDeal()
  {
    $this->layout->set('/home');
    $this->layout->data_json = json_encode(array(
      'path' => Sally::get('path'),
      'module' => $this->request->getModule(),
      'controller' => $this->request->getController(),
      'action' => $this->request->getAction()
    ));

    if ($this->request->getAction() == 'request') {
      $this->layout->disableLayout();
      $this->view->disableControllerView();
    }
  }
}