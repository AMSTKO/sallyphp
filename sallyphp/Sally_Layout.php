<?php
class Sally_Layout
{
  private $_layout = false;
  private $_enable = true;
  private $_content = false;
  protected static $_instance = false;

  public static function getInstance()
  {
    if (!self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function getContent()
  {
    return $this->_content;
  }

  public function load()
  {
    ob_start();
    require_once $this->_layout;
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
  }

  public function integrate($_content)
  {
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
    $sally = Sally::getInstance();
    list($layout_file, $layout_name) = $sally->getFile($name, 'layout');
    $this->_layout = $layout_file;
  }
}