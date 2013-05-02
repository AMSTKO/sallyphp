<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @copyright Copyright (c) 2013, Jonathan Amsellem.
 * @license   https://github.com/MrPing/sallyphp#license
 */

class Sally_Trafficker
{
  private $_preDealExec = false;
  private $traffickers = array();
  protected static $_instance = false;

  public function __construct()
  {
    $this->request = Sally_Request::getInstance();
  }

  public static function getInstance()
  {
    if (!self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function preDeal()
  {
    foreach ($this->traffickers as $object) {
      $object->preDeal();
    }
    $this->_preDealExec = true;
  }

  public function preDelivery()
  {
    foreach ($this->traffickers as $object) {
      $object->preDelivery();
    }
  }

  public function add($name)
  {
    $sally = Sally::getInstance();
    list($trafficker_file, $trafficker_name) = $sally->getFile($name, 'trafficker');
    require_once $trafficker_file;
    $trafficker = new $trafficker_name();
    array_push($this->traffickers, $trafficker);
  }

  public function preDealIsExec()
  {
    return $this->_preDealExec;
  }
}