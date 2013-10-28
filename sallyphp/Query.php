<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @license   https://github.com/MrPing/sallyphp#license
*/

namespace sally;

/**
 * Sally Query
*/
class Query
{
  /**
   * @var array
  */
  private $path = array();

  /**
   * Query constructor
   * @param mixed cumule les valeurs du chemin
  */
  public function __construct($path = false, $execute = false)
  {
    if ($path) {
      $this->path = $path;
    } else {
      $this->prepare = new QueryObject;
      $this->execute = new QueryObject(false, true);
    }
  }

  /**
   * Préparer une requête
   * @param string
   * @return object engine
  */
  public function prepare($request = '', $method = null, $data = array())
  {
    return new Engine($request, $method, $data);
  }

  /**
   * Executer directement une requête
   * @param string
   * @return mixed
  */
  public function execute($request = '', $method = null, $data = array())
  {
    $engine = new Engine($request, $method, $data);
    return $engine->execute();
  }
}