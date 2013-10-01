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
 * Sally Module
*/
class Module
{
  /**
   * @var array
  */
  private $_modules = array();

  /**
   * Ajouter un module utilisalbe pour l'application
   * @param string 'site', 'api', 'admin'
  */
  public function add($name)
  {
    $module_name = strtolower($name);
    try {
      if (!in_array($module_name, $this->_modules)) {
        if (!is_dir(\Sally::get('application') . '/modules/' . $module_name)) {
          throw new Exception('Le module "' . \Sally::get('application') . '/modules/' . $module_name .'" n\'existe pas.');
        }
        array_push($this->_modules, $module_name);
      }
    } catch (Exception $e) {
      exit;
    }
  }

  public function get()
  {
    return $this->_modules;
  }
}