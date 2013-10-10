<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
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
  private $_mainViewData = array();

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
  public function load($name, $data = array(), $main = false)
  {
    list($view_file, $view_fileName) = $this->engine->getFilePath($name, 'view');

    // tampon
    ob_start();

    // données pour la vue
    if ($main) {
      $data = $this->_mainViewData;
      foreach ($this->_mainViewData as $key => $row) {
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

    // fin du tampon
    $content = ob_get_contents();
    ob_end_clean();

    // modification possible du contenu de la vue
    $content = $this->trafficker->viewDelivery($content, $data);

    // livraison
    return $content;
  }

  /**
   * Définit des données de la vue
   * @param string, array value name or values array
   * @param mixed value
  */
  public function setData($data, $value = null)
  {
    if (is_string($data)) {
      $this->_mainViewData[$data] = $value;
    } elseif (is_array($data)) {
      $this->_mainViewData = array_merge($this->_mainViewData, $data);
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