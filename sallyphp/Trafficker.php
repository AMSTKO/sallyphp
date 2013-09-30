<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @copyright Copyright (c) 2013, Jonathan Amsellem.
 * @license   https://github.com/MrPing/sallyphp#license
 */

namespace sally;

/**
 * Sally
*/
class Trafficker
{
  private $_preDealExec = false;
  private $traffickers = array();
  protected static $_instance = false;

  public function __construct($engine)
  {
    $this->engine = $engine;
    $this->request = $engine->request;
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
    $sally = \Sally::getInstance();
    $trafficker_name = ucfirst($name) . 'Trafficker';
    $trafficker = new $trafficker_name($this->engine);
    array_push($this->traffickers, $trafficker);
  }

  public function preDealIsExec()
  {
    return $this->_preDealExec;
  }
}