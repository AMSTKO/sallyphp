<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @copyright Copyright (c) 2013, Jonathan Amsellem.
 * @license   https://github.com/MrPing/sallyphp#license
 */

class Controller
{
  public $view;

  public function __construct()
  {
    $this->view = View::getInstance();
    $this->request = Request::getInstance();
  }

  public function helper($name)
  {
    $helper = Helper::getInstance();
    return $helper->load($name);
  }

  public function redirect($url)
  {
    header('Location: ' . $url);
    exit;
  }

  public function forward($action = 'index', $controller = null, $module = null)
  {
    Sally::getInstance()->enableForward(array(
      'module' => $module,
      'controller' => $controller,
      'action' => $action
    ));
  }
}