<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @copyright Copyright (c) 2013, Jonathan Amsellem.
 * @license   https://github.com/MrPing/sallyphp#license
 */

class View
{
  private $_controllerView = true;
  private $_data = array();
  protected static $_instance = false;

  public static function getInstance()
  {
    if (!self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function load($name, $data = null, $main = false)
  {
    $sally = Sally::getInstance();
    list($view_file, $view_fileName) = $sally->getFile($name, 'view');
    ob_start();

    if (is_array($data)) {
      foreach ($data as $key => $row) {
        $$key = $row;
      }
    }

    if ($main) {
      foreach ($this->_data as $key => $row) {
        $$key = $row;
      }
    }

    require $view_file;
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
  }

  public function setData($data, $value = null)
  {
    if (is_string($data)) {
      $this->_data[$data] = $value;
    } elseif (is_array($data)) {
      $this->_data = array_merge($this->_data, $data);
    }
  }

  public function disableControllerView()
  {
    $this->_controllerView = false;
  }

  public function controllerViewIsEnabled()
  {
    return $this->_controllerView;
  }
}