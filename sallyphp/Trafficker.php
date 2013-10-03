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
 * Sally
*/
class Trafficker
{
  /**
   * @var object
  */
  private $engine;
  private $request;

  /**
   * @var boolean
  */
  private $_preEngineExecute = false;

  /**
   * @var array
  */
  private $_traffickers = array();
  private $_traffickers_modules = array();

  /**
   * Trafficker constructor
   * @param object
  */
  public function __construct($engine)
  {
    $this->engine = $engine;
    $this->request = $engine->request;
  }

  /**
   * Appelée au début de la requête
  */
  public function preEngine()
  {
    foreach ($this->_traffickers as $object) {
      $object->preEngine();
    }
    $this->_preEngineExecute = true;
  }


  /**
   * Appelée avant la livraison de la vue
   * @param string contenu
   * @return string contenu
  */
  public function viewDelivery($content, $data)
  {
    foreach ($this->_traffickers as $object) {
      $_content = $object->viewDelivery($content, $data);
      if ($_content !== null) {
        $content = $_content;
      }
    }
    return $content;
  }

  /**
   * Appelée avant d'intégrer le contenu au layout
   * @param string contenu
   * @return string contenu
  */
  public function preLayout($content)
  {
    foreach ($this->_traffickers as $object) {
      $_content = $object->preLayout($content);
      if ($_content !== null) {
        $content = $_content;
      }
    }
    return $content;
  }

  /**
   * Appelée avant la livraison du layout
   * @param string contenu
   * @return string contenu
  */
  public function layoutDelivery($content)
  {
    foreach ($this->_traffickers as $object) {
      $_content = $object->layoutDelivery($content);
      if ($_content !== null) {
        $content = $_content;
      }
    }
    return $content;
  }

  /**
   * Appelée avant de retourner le contenu de la réponse au client
   * @param string contenu
   * @return string contenu
  */
  public function engineDelivery($content, $databack)
  {
    foreach ($this->_traffickers as $object) {
      $_content = $object->engineDelivery($content, $databack);
      if ($_content != null) {
        $content = $_content;
      }
    }
    return $content;
  }

  /**
   * Ajouter un traffiquant
   * @param string name
  */
  public function add($name, $modules = array())
  {
    if (count($modules) == 0 || in_array($this->engine->request->getModule(), $modules)) {
      $sally = \Sally::getInstance();
      $trafficker_name = ucfirst($name) . 'Trafficker';
      $trafficker = new $trafficker_name($this->engine);
      array_push($this->_traffickers, $trafficker);
    }
  }

  /**
   * L'action preEngine a t elle été executée
   * @param string name
  */
  public function preEngineIsExecute()
  {
    return $this->_preEngineExecute;
  }
}