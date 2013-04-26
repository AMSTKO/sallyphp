<?php

class Sally_Session
{
  protected $_isLogged = false;
  protected $_content = array();
  protected static $_instance = false;

  public function __construct()
  {
    $this->rijndael = Sally_Rijndael::getInstance();

    // check cookie
    if($this->getCookie()) {
      if (isset($this->_content['logged']) && $this->_content['logged'] == 1) {
        $this->_isLogged = true;
      }
    } else {
      $this->setGuest();
    }
  }

  public static function getInstance()
  {
    if (!self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function isLogged()
  {
    return $this->_isLogged;
  }

  public function getIdentity()
  {
    return $this->_content;
  }

  public function set($name, $value)
  {
    $this->_content[$name] = $value;
  }

  public function get($name)
  {
    if (array_key_exists($name, $this->_content)) {
      return $this->_content[$name];
    } else {
      return false;
    }
  }

  public function signout()
  {
    $this->_isLogged = false;
    $this->setGuest();
  }

  protected function setGuest()
  {
    $this->_content = array(
      'logged' => 0,
      'guest' => uniqid('_', true)
    );
    $this->setCookie();
  }

  protected function getCookie()
  {
    if (isset($_COOKIE[Sally::get('cookie.name')])) {
      $content = $this->rijndael->decrypt($_COOKIE[Sally::get('cookie.name')]);
      $checksum = crc32(Sally::get('cookie.iv') . substr($content, 0, strrpos($content, '¤') + 2));

      // unserialize content
      $tmp = explode('¤', $content);
      foreach ($tmp as $row) {
        $tmp2 = explode('|', $row);
        if (count($tmp2) == 2) {
          $this->_content[$tmp2[0]] = $tmp2[1];
        }
      }

      // ctrl checksum
      if (!isset($this->_content['checksum']) || $this->_content['checksum'] != $checksum) {
        $this->signout();
        return false;
      }

      return true;
    } else {
      return false;
    }
  }

  public function setCookie()
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