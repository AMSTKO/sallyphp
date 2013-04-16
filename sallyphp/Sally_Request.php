<?php
class Sally_Request {

  public $module = false;
  public $controller = false;
  public $action = false;
  public $data = array();

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

  public function getRequest($name)
  {
    if (array_key_exists($name, $this->data)) {
      return $this->data[$name];
    } else {
      return false;
    }
  }
}