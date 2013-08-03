<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @copyright Copyright (c) 2013, Jonathan Amsellem.
 * @license   https://github.com/MrPing/sallyphp#license
 */

class Trafficker
{
  private $_preDealExec = false;
  private $traffickers = array();
  protected static $_instance = false;

  public function __construct()
  {
    $this->request = Request::getInstance();
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

  public function preLayout()
  {
    foreach ($this->traffickers as $object) {
      $object->preLayout();
    }
  }

  public function preDelivery()
  {
    foreach ($this->traffickers as $object) {
      $object->preDelivery();
    }
  }

  public function preView($out, $data)
  {
    foreach ($this->traffickers as $object) {
      $_out = $object->preView($out, $data);
      if ($_out !== null) {
        $out = $_out;
      }
    }
    return $out;
  }

  public function add($name)
  {
    $sally = Sally::getInstance();
    $trafficker_name = ucfirst($name) . 'Trafficker';
    $trafficker = new $trafficker_name();
    array_push($this->traffickers, $trafficker);
  }

  public function preDealIsExec()
  {
    return $this->_preDealExec;
  }
}