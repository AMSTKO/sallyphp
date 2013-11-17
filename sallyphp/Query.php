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
   * Traitement d'une propriété (execute ou prepare)
   * @param string
  */
  public function __get($name)
  {
    if ($name == 'execute') {
      return new QueryObject(false, true);
    } else {
      return new QueryObject;
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