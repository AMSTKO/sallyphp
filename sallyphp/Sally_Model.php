<?php

class Sally_Model
{

  protected $sally;
  protected $request;

  public function __construct()
  {
    $this->request = Sally_Request::getInstance();
  }

  public function model($name)
  {
    $sally = Sally::getInstance();
    list($model_file, $model_class_name) = $sally->getFile($name, 'model');
    require_once $model_file;
    return new $model_class_name();
  }
}