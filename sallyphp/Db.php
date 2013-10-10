<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @license   https://github.com/MrPing/sallyphp#license
*/

namespace sally;

/**
 * Sally Db
*/
class Db
{
  /**
   * @var array
  */
  private $_connections = array();

  /**
   * @var mixed
  */
  protected static $_instance = false;

  /**
   * Db instance
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
   * Récupérer une connexion
   * @param string connection name
   * @return object
  */
  public static function getConnection($name = null)
  {
    try {
      $instance = self::getInstance();
      if ($name) {
        if (array_key_exists($name, $instance->_connections)) {
          return $instance->_connections[$name];
        } else {
          throw new Exception('Connection introuvable.');
        }
      } else {
        return $instance->_connections['default'];
      }
    } catch (Exception $e) {
      exit;
    }
  }

  /**
   * Ajouter une connexion
   * @param array configuration
  */
  public function add($db)
  {
    try {
      if (!isset($db['type'])) {
        throw new Exception('Type de base de données non précisé.');
      }

      if (!isset($db['name'])) {
        throw new Exception('Veuillez préciser un nom pour cette connexion, par exemple "default".');
      }

      if (array_key_exists($db['name'], $this->_connections)) {
        throw new Exception('Ce nom de connexion est déjà utilisé.');
      }

      // mysql pdo
      if ($db['type'] == 'mysql-pdo') {

        // check
        if (isset($db['host']) && isset($db['dbname']) && isset($db['user']) && isset($db['passwd'])) {

          // connection
          $this->_connections[$db['name']] = new \PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'], $db['user'], $db['passwd']);
          $this->_connections[$db['name']]->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC); 

          if (isset($db['timezone'])) {
            $stmt = $this->_connections[$db['name']]->prepare('set names utf8; set time_zone = :time_zone');
            $stmt->execute(array('time_zone' => $db['timezone']));
          } else {
            $this->_connections[$db['name']]->exec('set names utf8');
          }
        } else {
          throw new Exception('Configuration mysql-pdo invalide');
        }
      } 

      // redis
      else if ($db['type'] == 'redis-predis') {

        // check
         if (isset($db['host']) && isset($db['port'])) {

          // get Predis library      
          $sally = \Sally::getInstance();
          $sally->library('Predis/autoload.php');

          // connection
          $this->_connections[$db['name']] = new \Predis\Client();

          try {
            $this->_connections[$db['name']]->connect(array(
              'host' => $db['host'], 
              'port' => $db['port'] // default 6379
            ));
          } catch(\Predis\CommunicationException $e) {
            $this->_connections[$db['name']] = false;
          }
        } else {
          throw new Exception('Configuration predis invalide');
        }
      } 

      // other
      else {
        throw new Exception('Type de base de données indisponible.');
      }
    } catch (Exception $e) {
      exit;
    }
  }
}