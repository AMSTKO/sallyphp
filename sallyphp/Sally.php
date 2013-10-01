<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @copyright Copyright (c) 2013, Jonathan Amsellem.
 * @license   https://github.com/MrPing/sallyphp#license
 */

/**
 * Sally
*/
class Sally
{
  /**
   * Environnement
   * @var string
  */
  const name = 'SallyPHP';
  const version = '1.130930.1';
  const path = __DIR__;

  /**
   * Configuration
   * @var array
  */
  private $_cfg = array();
  
  /**
   * Instance
   * @var mixed
  */
  protected static $_instance = false;

  /**
   * Initialisation de Sally
  */
  private function __construct()
  {
    header('Content-Type: text/html; charset=utf-8');
    spl_autoload_register(array($this, 'classLoader'));

    $this->_cfg['application'] = $_SERVER['DOCUMENT_ROOT'] . '/application';
    $this->_cfg['path'] = '/';
    $this->_cfg['controller.default'] = 'index';
    $this->_cfg['module.default'] = 'site';
    $this->_cfg['rijndael.key'] = 'define a key';
    $this->_cfg['cookie.domain'] = null;
    $this->_cfg['cookie.name'] = 'sally';
    $this->_cfg['modules'] = array();
  }

  /**
   * Instance de Sally
   * @return object
  */
  public static function getInstance()
  {
    if (!self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  /**
   * Chargement d'une requête
   * @param string 'MyTrafficker', 'UserModel', 'Site_UserModel' 'sally\Request'
  */
  public function load($request_string = '', $traffickers = array(), $helpers = array())
  {
    try {
      $engine = new sally\Engine($request_string, $traffickers, $helpers);
      return $engine->call();
    } catch (sally\Exception $e) {
      
    }
  }

  /**
   * Chargement automatique des class Sally, Models et Traffickers
   * @param string 'MyTrafficker', 'UserModel', 'Site_UserModel' 'sally\Request'
  */
  public function classLoader($class)
  {
    $segments = preg_split("/(?=[A-Z_\\\])/", $class, 0, PREG_SPLIT_NO_EMPTY);
    $first = current($segments);
    $last = end($segments);
    $segmentsNb = count($segments);
    $error = true;

    // Model
    if ($last == 'Model' && $segmentsNb > 1) {
      if (isset($segments[1]) && $segments[1] == '_') {
        $path = Sally::get('application') . '/modules/' . strtolower($segments[0]) . '/models/' . $class . '.php';
      } else {
        $path = Sally::get('application') . '/models/' . $class . '.php';
      }
    } 

    // Trafficker
    elseif ($last == 'Trafficker' && $segmentsNb > 1 && $first != 'sally') {
      $path = Sally::get('application') . '/traffickers/' . $class . '.php';
    }

    // Sally
    elseif ($first == 'sally') {
      $_segments = explode("\\", $class);
      $class_name = $_segments[1];
      $path = Sally::path . '/' . $class_name . '.php';
    }

    // pas concerné
    else {
      return false;
    }

    try {
      if (!file_exists($path)) {
        throw new sally\Exception('Le fichier ' . $class . ' "' . $path . '" n\'existe pas.');
      }
    } catch (sally\Exception $e) {

    }

    require_once $path;
  }

  /**
   * Ajouter un module utilisalbe pour l'application
   * @param string ex: 'site', 'api', 'admin'
  */
  public function addModule($name)
  {
    $modules = Sally::get('modules');
    $module_name = strtolower($name);
    try {
      if (!in_array($module_name, $modules)) {
        if (!is_dir(Sally::get('application') . '/modules/' . $module_name)) {
          throw new sally\Exception('Le module "' . Sally::get('application') . '/modules/' . $module_name .'" n\'existe pas.');
        }
        array_push($modules, $module_name);
      }
    } catch (sally\Exception $e) {

    }
    Sally::set('modules', $modules);
  }

  /**
   * Récupérer un paramètre global
   * @param string ex: 'application' 'user'[, 'id']
   * @return mixed
  */
  public static function get()
  {
    $instance = self::getInstance();

    if (func_num_args() === 2) {
      $domain = func_get_arg(0);
      $name = func_get_arg(1);
      if (array_key_exists($name, $instance->_cfg[$domain])) {
        return $instance->_cfg[$domain][$name];
      }
    } else {
      $name = func_get_arg(0);
      if (array_key_exists($name, $instance->_cfg)) {
        return $instance->_cfg[$name];
      }
    }
    return null;
  }

  /**
   * Définit un paramètre global
   * @param string ex: 'name1', 'value1'|'user', 'id', 12
  */
  public static function set()
  {
    $instance = self::getInstance();

    if (func_num_args() === 3) {
      $domain = func_get_arg(0);
      $name = func_get_arg(1);
      $value = func_get_arg(2);

      if (!array_key_exists($domain, $instance->_cfg)) {
        $instance->_cfg[$domain] = array();
      }
      $instance->_cfg[$domain][$name] = $value;
    } else {
      $name = func_get_arg(0);
      $value = func_get_arg(1);
      $instance->_cfg[$name] = $value;
    }
  }

  /**
   * Chargement d'une librairie
   * @param string
  */
  public function getLibrary($path)
  {
    require_once Sally::get('application') . '/libs/' . $path;
  }
}