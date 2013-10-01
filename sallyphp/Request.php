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
 * Sally Request
*/
class Request
{
  private $_module = false;
  private $_controller = false;
  private $_action = false;
  private $_data = array();

  public function __construct()
  {
    
  }

  /**
   * Analyse le chemin de la requête et définit le model, 
   * le controleur, l'action ainsi que les données.
   * @param string '/site/index/signin'
   * @return boolean
  */
  public function path($request_string)
  {
    // modules dispnibles
    $modules = \Sally::get('modules');
    $has_module = count($modules) > 0 ? true : false;

    $request_explode = explode('/', $request_string);
    $request_index = 0;
    $passe = false;

    // nom des principaux éléments
    $module_name = null;
    $controller_name = null;
    $action_name = null;

    // ordre logique des éléments
    $logic_controller_index = 0;
    $logic_action_index = 1;
    $logic_data_index = 2;

    // analyse des éléments
    foreach ($request_explode as $key => $element) {
      $element = strtolower($element);
      
      // Sauter un element lors de la définition des données puisque: 1 donnée = 2 éléments.
      if ($passe) {
        $passe = false;
        continue;
      }

      if (empty($element)) {
        continue;
      }
      
      if ($request_index >= $logic_data_index) {
        if (isset($request_explode[($key + 1)])) {
          // définition des données
          $this->setSegment($element, $request_explode[($key + 1)]);
          $passe = true;
        }
      } else {
        // définition du module
        if ($has_module) {
          if ($request_index === 0 && in_array($element, $modules)) {
            $module_name = $element;
            $logic_controller_index++;
            $logic_action_index++;
            $logic_data_index++;
          }
        }

        // définition du controleur
        if ($request_index == $logic_controller_index) {
          $controller_name = $element;
        }

        // définition de l'action
        if ($request_index == $logic_action_index) {
          $action_name = $element;
        }
      }

      $request_index++;
    }

    if ($has_module) {
      if ($module_name !== null) {
        $this->setModule($module_name);
      } else {
        $this->setModule(strtolower(\Sally::get('module.default')));
      }
    }

    if ($controller_name != null) {
      $this->setController($controller_name);
    } else {
      $this->setController(strtolower(\Sally::get('controller.default')));

    }

    if ($action_name != null) {
      $this->setAction($action_name);
    } else {
      $this->setAction('index');
    }

    return true;
  }

  public function setSegment($name, $value)
  {
    $this->_data[$name] = $value;
  }

  public function getSegment($name)
  {
    if (array_key_exists($name, $this->_data)) {
      return $this->_data[$name];
    } else {
      return null;
    }
  }

  public function getPost($name)
  {
    if (array_key_exists($name, $_POST)) {
      return $_POST[$name];
    } else {
      return null;
    }
  }

  public function setModule($name)
  {
    $this->_module = $name;
  }

  public function setController($name)
  {
    $this->_controller = $name;
  }

  public function setAction($name)
  {
    $this->_action = $name;
  }

  public function getModule()
  {
    return $this->_module;
  }

  public function getController()
  {
    return $this->_controller;
  }

  public function getAction()
  {
    return $this->_action;
  }
}