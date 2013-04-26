<?php
class Sally_Helper
{

  protected $sally;
  protected static $_instance = false;

  public function __construct()
  {
    $this->sally = Sally::getInstance();
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
    list($helper_file, $helper_name) = $this->sally->getFile($name, 'helper');
    require_once $helper_file;
  }
}