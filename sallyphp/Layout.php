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
 * Sally Layout
*/
class Layout
{
  /**
   * @var object
  */
  private $engine;
  private $request;
  private $trafficker;

  /**
   * @var boolean
  */
  private $_layout = false;
  private $_enable = true;
  private $_content = false;

  /**
   * @var array
  */
  private $_data = array();

  /**
   * Layout constructor
   * @param object
  */
  public function __construct($engine)
  {
    $this->engine = $engine;
    $this->request = $engine->request;
    $this->trafficker = $engine->trafficker;
  }

  public function getContent()
  {
    return $this->_content;
  }

  public function load()
  {
    ob_start();

    foreach ($this->_data as $key => $row) {
      $$key = $row;
    }
      
    require_once $this->_layout;
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
  }

  public function integrate($_content)
  {
    $this->trafficker->preLayout();
    $this->_content = $_content;
    return $this->load();
  }

  public function isDefined()
  {
    if ($this->_layout) {
      return true;
    } else {
      return false;
    }
  }

  public function isEnabled()
  {
    return $this->_enable;
  }

  public function disableLayout()
  {
    $this->_enable = false;
  }

  public function set($name)
  {
    $sally = \Sally::getInstance();
    list($layout_file, $layout_name) = $this->engine->getFilePath($name, 'layout');
    $this->_layout = $layout_file;
  }

  public function setData($data, $value = null)
  {
    if (is_string($data)) {
      $this->_data[$data] = $value;
    } elseif (is_array($data)) {
      $this->_data = array_merge($this->_data, $data);
    }
  }

  public function getData($data)
  {
    if (array_key_exists($data, $this->_data)) {
      return $this->_data[$data];
    } else {
      return false;
    }
  }
}