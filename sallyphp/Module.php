<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @license   https://github.com/MrPing/sallyphp#license
*/

namespace sally;

/**
 * Sally Module
*/
class Module
{
  /**
   * @var array
  */
  private $_modules = array();
  private $_modules_traffickers = array();

  /**
   * Ajouter un module utilisalbe pour l'application
   * @param string 'site', 'api', 'admin'
  */
  public function add($name, $traffickers = array())
  {
    $module_name = strtolower($name);
    try {
      if (!in_array($module_name, $this->_modules)) {
        if (!is_dir(\Sally::get('application') . '/modules/' . $module_name)) {
          throw new Exception('Le module "' . \Sally::get('application') . '/modules/' . $module_name .'" n\'existe pas.');
        }
        array_push($this->_modules, $module_name);
        $this->_modules_traffickers[$module_name] = $traffickers;
      }
    } catch (Exception $e) {
      exit;
    }
  }

  /**
   * Récupère la liste des modules
   * @return array
  */
  public function get()
  {
    return $this->_modules;
  }

  /**
   * Récupère la liste des trafiquants pour un module
   * @param string module name
   * @return array
  */
  public function getTraffickersForModule($module_name)
  {
    if (array_key_exists($module_name, $this->_modules_traffickers)) {
      return $this->_modules_traffickers[$module_name];
    } else {
      return array();
    }
  }
}