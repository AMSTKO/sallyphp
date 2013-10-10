<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @license   https://github.com/MrPing/sallyphp#license
*/

namespace sally;

/**
 * Sally Layout
*/
class Layout
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
  private $_layout = false;
  private $_enable = true;
  private $_content = false;

  /**
   * @var array
  */
  private $_data = array();

  /**
   * Layout constructor
   * @param object
  */
  public function __construct($engine)
  {
    $this->engine = $engine;
    $this->request = $engine->request;
    $this->trafficker = $engine->trafficker;
  }

  /**
   * Récupérer le contenu à placer dans le layout
   * @return string
  */
  public function getContent()
  {
    return $this->_content;
  }

  /**
   * Charge le layout et rend disponible les données définies
   * @return string
  */
  private function load()
  {
    // tampon
    ob_start();

    // données pour le layout
    foreach ($this->_data as $key => $row) {
      $$key = $row;
    }

    require $this->_layout;

    // fin du tampon
    $content = ob_get_contents();
    ob_end_clean();

    // livraison
    return $content;
  }

  /**
   * Savoir si le layout a été correctement définit
   * @param string content
   * @return string content in layout
  */
  public function integrate($content)
  {
    $content = $this->trafficker->preLayout($content);
    $this->_content = $content;
    return $this->load();
  }

  /**
   * Savoir si le layout a été correctement définit
   * @return boolean
  */
  public function isDefined()
  {
    if ($this->_layout) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Savoir si le layout est activé ou non
   * @return boolean
  */
  public function isEnabled()
  {
    return $this->_enable;
  }

  /**
   * Désactive le layout
  */
  public function disableLayout()
  {
    $this->_enable = false;
  }

  /**
   * Définit le layout
   * @param string
  */
  public function set($name)
  {
    list($layout_file, $layout_name) = $this->engine->getFilePath($name, 'layout');
    $this->_layout = $layout_file;
  }

  /**
   * Définit des données au layout
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
   * Récupérer une donnée définit au layout
   * @param string value name
   * @return mixed value
  */
  public function getData($data)
  {
    if (array_key_exists($data, $this->_data)) {
      return $this->_data[$data];
    } else {
      return false;
    }
  }
}