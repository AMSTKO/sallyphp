<?php

class OutirlTrafficker extends Sally_Trafficker_Abstract
{
  function __construct()
  {
    $this->acl = Sally_Acl::getInstance();
    $this->session = Sally_Session::getInstance();
    $this->layout = Sally_Layout::getInstance();
  }

  function preDeal()
  {
    $this->layout->set('/home');
    $this->session->setCookie();
  }

  function preDelivery()
  {
    //echo 'preDelivery!!!';
  }
}