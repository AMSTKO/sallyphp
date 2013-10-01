<?php

class MyTrafficker extends sally\TraffickerAbstract
{
  function preEngine()
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