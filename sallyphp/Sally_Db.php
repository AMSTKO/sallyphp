<?php

class Sally_Db {

  public $_connection = array();
  protected static $_instance = false;

  public static function getInstance()
  {
    if (!self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public static function getConnection($name = null)
  {
    $instance = self::getInstance();
    if (isset($name)) {
      if (array_key_exists($name, $instance->_connection)) {
        return $instance->_connection[$name];
      } else {
        throw new Exception('Connection introuvable.');
      }
    } else {
      return $instance->_connection['default'];
    }
  }

  public function add($cfg)
  {
    if (isset($cfg['type'])) {
      if ($cfg['type'] == 'mysql_pdo') {
        if (isset($cfg['host']) && isset($cfg['dbname']) && isset($cfg['user']) && isset($cfg['passwd'])) {
          if (isset($cfg['name'])) {
            $name = $cfg['name'];
          } else {
            $name = 'default';
          }
          $this->_connection[$name] = new PDO('mysql:host=' . $cfg['host'] . ';dbname=' . $cfg['dbname'], $cfg['user'], $cfg['passwd']);
        } else {
          throw new Exception('Configuration mysql invalide');
        }
      } else {
        throw new Exception('Type de db indisponible.');
      }
    } else {
      throw new Exception('Type de db non précisé.');
    }
  }
}