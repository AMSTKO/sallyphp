<?php
class Sally_Layout
{

  public $request;
  public $use = false;
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

  public function integrate($content)
  {
    ob_start();
    require_once $this->use;
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
  }

  public function set($name)
  {
    $sally = Sally::getInstance();
    list($layout_file, $layout_name) = $sally->getFile($name, 'layout');
    if (!file_exists($layout_file)) {
      throw new Exception('Le layout "' . $layout_name . '" n\'existe pas.');
    }
    $this->use = $layout_file;
  }
}