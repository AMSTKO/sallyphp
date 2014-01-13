<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @license   https://github.com/MrPing/sallyphp#license
*/

namespace sally;

/**
 * Sally Router
*/
class Router
{
  /**
   * @var array
  */
  private $_routes = array();

  /**
   * check request uri with routes
   * @param string
   * @param string
  */
  public function check($request)
  {
    if (isset($this->_routes[$request])) {
      return $this->_routes[$request];
    }

    foreach ($this->_routes as $pattern => $val) {
      if (preg_match('#^'.$pattern.'$#', $request)) {
        return preg_replace('#^'.$pattern.'$#', $val, $request);
      }
    }
    
    return $request;
  }

  /**
   * set data
   * @param string
   * @param string
  */
  public function set($pattern, $value)
  {
    $this->_routes[$pattern] = $value;
  }
}