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
 * Sally Exception
*/
class Exception extends \Exception
{
  public function __construct($message, $code = 0, Exception $previous = null)
  {
    $tmp = '';
    $tmp.= '<style>#spException{font-family: Verdana; font-size: 14px}#spException h2{color: #212121}#spException p{padding-left: 20px}</style>';
    $tmp.= '<div id="spException">';
      $tmp.= '<h2>bad trip</h2>';
      $tmp.= '<p>';
        $tmp.= '<b>' . $message . '</b><br>'; 
        $tmp.= '<i>at line </i><b>' . $this->getLine() . '</b>';
        $tmp.= '<i> in file </i><b>' . $this->getFile() . '</b><br><br>';
        foreach ($this->getTrace() as $id => $trace) {
          $tmp.= '<b>' . $id . '</b> ' . $trace['file'] . ' line ' . $trace['line'] . '<br>';
        }
        $tmp.= '<br>';
        $tmp.= '<small>' . \Sally::name . ' ' . \Sally::version . '</small>';
      $tmp.= '<p>';
    $tmp.= '</div>';
    echo $tmp;
    parent::__construct($message, $code, $previous);
  }
}