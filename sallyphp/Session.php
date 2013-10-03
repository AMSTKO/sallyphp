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
 * Sally Session
*/
class Session
{
  /**
   * @var array
  */
  private $_content = array();

  /**
   * @var boolean
  */
  private $_hasCookie = false;
  private $_isSet = false;

  /**
   * @var mixed
  */
  protected static $_instance = false;


  /**
   * Session constructor
  */
  private function __construct()
  {
    $this->rijndael = Rijndael::getInstance();
    if ($this->getCookie()) {
      $this->_hasCookie = true;
    } else {
      $this->_hasCookie = false;
    }
  }

  /**
   * Session instance
   * @return object
  */
  public static function getInstance()
  {
    if (!self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  /**
   * Définit le cookie si il a été modifié
  */
  public function sendHeaderCookie()
  {
    if ($this->_isSet) {
      $this->setCookie();
    }
  }

  /**
   * Présence du cookie ou non
   * @return boolean
  */
  public function hasCookie()
  {
    return $this->_hasCookie;
  }

  /**
   * Récupere le contenu du cookie
   * @return array
  */
  public function getContent()
  {
    return $this->_content;
  }

  /**
   * Définir entierrement le contenu du cookie
   * @param array
  */
  public function setContent($content = array())
  {
    $this->_content = $content;
    $this->_isSet = true;
  }

  /**
   * Définit une valeur du cookie
   * @param string name
   * @param string value
  */
  public function set($name, $value)
  {
    $this->_content[$name] = $value;
    $this->_isSet = true;
  }

  /**
   * Récupère une valeur du cookie
   * @param string name
   * @return mixed
  */
  public function get($name)
  {
    if (array_key_exists($name, $this->_content)) {
      return $this->_content[$name];
    } else {
      return false;
    }
  }

  /**
   * Récupère et décrypte le cookie
   * @return boolean
  */
  protected function getCookie()
  {
    if (isset($_COOKIE[\Sally::get('cookie.name')])) {
      $content = $this->rijndael->decrypt($_COOKIE[\Sally::get('cookie.name')]);
      $checksum = crc32(\Sally::get('cookie.iv') . substr($content, 0, strrpos($content, '¤') + 2));

      $tmp = explode('¤', $content);
      foreach ($tmp as $row) {
        $tmp2 = explode('|', $row);
        if (count($tmp2) == 2) {
          $this->_content[$tmp2[0]] = $tmp2[1];
        }
      }

      if (!isset($this->_content['checksum']) || $this->_content['checksum'] != $checksum) {
        $this->setContent();
        return false;
      }

      return true;
    } else {
      return false;
    }
  }

  /**
   * Définit le cookie
  */
  protected function setCookie()
  {
    $cookie = '';
    foreach ($this->_content as $key => $row) {
      $cookie.= $key.'|'.$row.'¤';
    }
    $cookie.= 'checksum|' . crc32(\Sally::get('cookie.iv') . $cookie);
    $content = $this->rijndael->encrypt($cookie);
    $expire = time()+60*60*24*7;

    setcookie(\Sally::get('cookie.name'), $content, $expire, '/', \Sally::get('cookie.domain'), 0, true);
  }
}