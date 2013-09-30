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
abstract class TraffickerAbstract
{
  function __construct($engine)
  {
    $this->engine = $engine;
    $this->request = $engine->request;
    $this->layout = $engine->layout;
    $this->view = $engine->view;
  }

  function preDeal() {}
  function preView() {}
  function preLayout() {}
  function preDelivery() {}
}