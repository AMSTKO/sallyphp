<?php

class MyTrafficker extends TraffickerAbstract
{
  function __construct()
  {
    $this->layout = Layout::getInstance();
    $this->view = View::getInstance();
    $this->request = Request::getInstance();
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