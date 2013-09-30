<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @copyright Copyright (c) 2013, Jonathan Amsellem.
 * @license   https://github.com/MrPing/sallyphp#license
 */

class Session
{
  private $_hasCookie = false;
  private $_content = array();
  private $_isSet = false;
  protected static $_instance = false;

  public function __construct()
  {
    $this->rijndael = Rijndael::getInstance();
    if ($this->getCookie()) {
      $this->_hasCookie = true;
    } else {
      $this->_hasCookie = false;
    }
  }

  public function sendHeaderCookie()
  {
    if ($this->_isSet) {
      $this->setCookie();
    }
  }

  public static function getInstance()
  {
    if (!self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function hasCookie()
  {
    return $this->_hasCookie;
  }

  public function getContent()
  {
    return $this->_content;
  }

  public function setContent($content = array())
  {
    $this->_content = $content;
    $this->_isSet = true;
  }

  public function set($name, $value)
  {
    $this->_content[$name] = $value;
    $this->_isSet = true;
  }

  public function get($name)
  {
    if (array_key_exists($name, $this->_content)) {
      return $this->_content[$name];
    } else {
      return false;
    }
  }

  protected function getCookie()
  {
    if (isset($_COOKIE[Sally::get('cookie.name')])) {
      $content = $this->rijndael->decrypt($_COOKIE[Sally::get('cookie.name')]);
      $checksum = crc32(Sally::get('cookie.iv') . substr($content, 0, strrpos($content, '¤') + 2));

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

  protected function setCookie()
  {
    $cookie = '';
    foreach ($this->_content as $key => $row) {
      $cookie.= $key.'|'.$row.'¤';
    }
    $cookie.= 'checksum|' . crc32(Sally::get('cookie.iv') . $cookie);
    $content = $this->rijndael->encrypt($cookie);
    $expire = time()+60*60*24*7;

    setcookie(Sally::get('cookie.name'), $content, $expire, '/', Sally::get('cookie.domain'), 0, true);
  }
}