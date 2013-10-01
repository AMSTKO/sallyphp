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
 * Sally Rijndael
*/
class Rijndael
{
  /**
   * @var boolean
  */
  private $_key = false;

  /**
   * Rijndael instance
   * @var mixed
  */
  protected static $_instance = false;

  /**
   * Rijndael constructor
   * @param string key
  */
  public function __construct($key = null)
  {
    if ($key) {
      $this->setKey($key);
    }
  }

  /**
   * Rijndael get instance
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
   * Définir une clef de cryptage
   * @param string
  */
  public function setKey($key)
  {
    $this->_key = $key;
  }

  /**
   * Encrypt
   * @param string
   * @return string
  */
  public function encrypt($encrypt)
  {
    $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_RAND);
    $passcrypt = trim(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->_key, trim($encrypt), MCRYPT_MODE_ECB, $iv));
    $encode = base64_encode($passcrypt);
    return $encode;
  }

  /**
   * Decrypt
   * @param string
   * @return string
  */
  public function decrypt($decrypt)
  {
    $decoded = base64_decode($decrypt);
    $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_RAND);
    $decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->_key, trim($decoded), MCRYPT_MODE_ECB, $iv));
    return $decrypted;
  }
}