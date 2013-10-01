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
 * Sally View
*/
class View
{
  /**
   * @var object
  */
  private $engine;
  private $request;
  private $trafficker;

  /**
   * @var boolean
  */
  private $_controllerViewIsEnabled = true;

  /**
   * @var array
  */
  private $_data = array();

  /**
   * View constructor
   * @param object
  */
  public function __construct($engine)
  {
    $this->engine = $engine;
    $this->request = $engine->request;
    $this->trafficker = $engine->trafficker;
  }

  /**
   * Charge une vue
   * @param string view name
   * @param array data
   * @param boolean is main view
  */
  public function load($name, $data = null, $main = false)
  {
    list($view_file, $view_fileName) = $this->engine->getFilePath($name, 'view');

    // tampon
    ob_start();

    if ($main) {
      foreach ($this->_data as $key => $row) {
        $$key = $row;
      }
    } else {
      if (is_array($data)) {
        foreach ($data as $key => $row) {
          $$key = $row;
        }
      }
    }

    require $view_file;
    $out = ob_get_contents();
    ob_end_clean();
    $out = $this->trafficker->preView($out, $data);
    return $out;
  }

  /**
   * Définit des données de la vue
   * @param string value name
   * @param mixed value
  */
  public function setData($data, $value = null)
  {
    if (is_string($data)) {
      $this->_data[$data] = $value;
    } elseif (is_array($data)) {
      $this->_data = array_merge($this->_data, $data);
    }
  }

  /**
   * Désactive la vue par defaut du controleur
  */
  public function disableControllerView()
  {
    $this->_controllerViewIsEnabled = false;
  }

  /**
   * Savoir si la vue par defaut du controleur est activé
   * @return boolean
  */
  public function controllerViewIsEnabled()
  {
    return $this->_controllerViewIsEnabled;
  }
}