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
 * Sally Helper
*/
class Helper
{
  /**
   * @var object
  */
  private $engine;

  /**
   * Helper constructor
   * @param object
  */
  public function __construct($engine)
  {
    $this->engine = $engine;
  }

  /**
   * charger un helper
   * @param string
  */
  public function add($name)
  {
    list($helper_file, $helper_name) = $this->engine->getFilePath($name, 'helper');
    require_once $helper_file;
  }
}