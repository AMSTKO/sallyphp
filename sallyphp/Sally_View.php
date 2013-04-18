<?php

class Sally_View
{
  public $_display = true;
  protected static $_instance = false;

  public function __construct()
  {
  }

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
    list($view_file, $view_fileName) = $sally->getFile($name, 'view');
    require_once $view_file;
  }
}