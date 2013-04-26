<?php

class Sally_Model
{
  protected static $_instance = false;

  public static function getInstance()
  {
    if (!self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function load($name)
  {
    $sally = Sally::getInstance();
    list($model_file, $model_class_name) = $sally->getFile($name, 'model');
    require_once $model_file;
    return new $model_class_name();
  }
}