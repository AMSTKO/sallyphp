<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @license   https://github.com/MrPing/sallyphp#license
*/

namespace sally;

/**
 * Sally Request
*/
class Request
{
  /**
   * @var boolean
  */
  private $_method = false;
  private $_module = false;
  private $_controller = false;
  private $_action = false;

  /**
   * @var array
  */
  private $_segment = array();
  private $_data = array();

  /**
   * Request constructor
   * @param string method POST, GET, DELETE, PUT...
  */
  public function __construct($method = null, $data = array())
  {
    if ($method) {
      $this->setMethod($method);
      if ($method == 'POST') {
        $this->_data = $data;
      }
    } else {
      $this->setMethod($_SERVER['REQUEST_METHOD']);
      $this->_data = $_POST;
    }
  }

  /**
   * Analyse le chemin de la requête et définit le model, 
   * le controleur, l'action ainsi que les données.
   * @param string '/site/index/signin'
   * @return boolean
  */
  public function path($request_string = '')
  {
    // modules dispnibles
    $sally = \Sally::getInstance();
    $modules = $sally->module->get();

    $has_module = count($modules) > 0 ? true : false;

    $request_explode = explode('?', $request_string);

    // path and datas
    $path = $request_explode[0];
    $datas = null;
    if (isset($request_explode[1])) {
      $datas = $request_explode[1];
    }

    // nom des principaux éléments
    $module_name = null;
    $controller_name = null;
    $action_name = null;

    // ordre logique des éléments
    $logic_controller_index = 0;
    $logic_action_index = 1;
    $logic_data_index = 2;

    // analyse des éléments "path"
    $path_explode = explode('/', $path);
    $path_index = 0;

    foreach ($path_explode as $key => $element) {
      $element = strtolower($element);
      
      if (empty($element)) {
        continue;
      }
      
      // définition du module
      if ($has_module) {
        if ($path_index === 0 && in_array($element, $modules)) {
          $module_name = $element;
          $logic_controller_index++;
          $logic_action_index++;
          $logic_data_index++;
        }
      }

      // définition du controleur
      if ($path_index == $logic_controller_index) {
        $controller_name = $element;
      }

      // définition de l'action
      if ($path_index == $logic_action_index) {
        $action_name = $element;
      }

      $path_index++;
    }

    // analyse des éléments "data"
    if ($datas) {
      $datas_explode = explode('&', $datas);
      $datas_index = 0;
      
      foreach ($datas_explode as $key => $element) {
        $element = strtolower($element);
        
        if (empty($element)) {
          continue;
        }

        $element_explode = explode('=', $element);

        if (!empty($element_explode[0]) && isset($element_explode[1])) {
          $this->setSegment($element_explode[0], $element_explode[1]);
        }
      }
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

    if ($action_name != null && substr($action_name, 0, 1) != '_') {
      $this->setAction($action_name);
    } else {
      $this->setAction('index');
    }

    return true;
  }

  /**
   * set method
   * @param string method POST, GET, DELETE, PUT...
  */
  public function setMethod($value)
  {
    $this->_method = $value;
  }

  /**
   * get method
   * @return string method POST, GET, DELETE, PUT...
  */
  public function getMethod()
  {
    return $this->_method;
  }

  /**
   * set segment
   * @param string
   * @param string
  */
  public function setSegment($name, $value)
  {
    $this->_segment[$name] = $value;
  }

  /**
   * get segment
   * @param string
   * @return string
  */
  public function getSegment($name)
  {
    if (array_key_exists($name, $this->_segment)) {
      return $this->_segment[$name];
    } else {
      return false;
    }
  }

  /**
   * set data
   * @param string
   * @param string
  */
  public function setData($name, $value)
  {
    $this->_data[$name] = $value;
  }

  /**
   * get data
   * @param string
   * @return string
  */
  public function getData($name)
  {
    if (array_key_exists($name, $this->_data)) {
      return $this->_data[$name];
    } else {
      return false;
    }
  }

  /**
   * set module
   * @param string
  */
  public function setModule($name)
  {
    $this->_module = $name;
  }

  /**
   * set controller
   * @param string
  */
  public function setController($name)
  {
    $this->_controller = $name;
  }

  /**
   * set action
   * @param string
  */
  public function setAction($name)
  {
    $this->_action = $name;
  }

  /**
   * get module
   * @return string
  */
  public function getModule()
  {
    return $this->_module;
  }

  /**
   * get controller
   * @return string
  */
  public function getController()
  {
    return $this->_controller;
  }

  /**
   * get action
   * @return string
  */
  public function getAction()
  {
    return $this->_action;
  }
}