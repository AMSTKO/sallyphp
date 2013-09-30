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
 * Sally Controller
*/
class Controller
{
  /**
   * @var object
  */
  public $engine;
  public $request;
  public $layout;
  public $view;
  public $helper;

  /**
   * Controller constructor
   * @param object
  */
  public function __construct($engine)
  {
    $this->engine = $engine;
    $this->request = $engine->request;
    $this->layout = $engine->layout;
    $this->view = $engine->view;
    $this->helper = $engine->helper;
  }

  /**
   * Raccourci pour Charger un helper
   * @param string
  */
  public function helper($name)
  {
    $this->helper->add($name);
  }

  /**
   * Raccourci pour définir une redirection client
   * @param string
  */
  public function redirect($url)
  {
    $this->engine->setRedirect($url);
  }

  /**
   * Raccourci pour définir un forward (redirection interne)
   * @param string action name
   * @param string controller name
   * @param string module name
  */
  public function forward($action = 'index', $controller = null, $module = null)
  {
    $this->engine->setForward(array(
      'module' => $module,
      'controller' => $controller,
      'action' => $action
    ));
  }
}