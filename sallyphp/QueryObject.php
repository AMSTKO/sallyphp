<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @license   https://github.com/MrPing/sallyphp#license
*/

namespace sally;

/**
 * Sally QueryObject
*/
class QueryObject
{
  /**
   * @var array
  */
  private $path = array();

  /**
   * @var boolean
  */
  private $execute = false;

  /**
   * QueryObject constructor
   * @param mixed cumule les valeurs du chemin
  */
  public function __construct($path = false, $execute = false)
  {
    if ($path) {
      $this->path = $path;
    }
    $this->execute = $execute;
  }

  /**
   * Traitement d'une propriété (méthode, module, controleur)
   * @param string
  */
  public function __get($name)
  {
    array_push($this->path, $name);
    return new QueryObject($this->path, $this->execute);
  }

  /**
   * Traitement d'une méthode (action)
   * @param string
   * @param array
  */
  public function __call($name, $arguments)
  {
    if (!isset($this->path[0])) {
      throw new Exception('Vous devez au moins spécifier la méthode à utiliser (GET, POST...).');
    }

    // récupèrer les donénes
    if (isset($arguments[0])) {
      $data = $arguments[0];
    } else {
      $data = array();
    }

    // récupère la méthode
    $method = strtoupper($this->path[0]);

    // retire le nom de la méthode du chemin
    array_shift($this->path);

    // ajouter le nom de l'action au chemin
    array_push($this->path, $name);

    $engine = new Engine($this->path, $method, $data);

    if ($this->execute) {
      return $engine->execute();
    } else {
      return $engine;
    }
  }
}