<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @copyright Copyright (c) 2013, Jonathan Amsellem.
 * @license   https://github.com/MrPing/sallyphp#license
 */

class Model
{
  protected static $_instance = false;

  public static function getInstance()
  {
    if (!self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }
}