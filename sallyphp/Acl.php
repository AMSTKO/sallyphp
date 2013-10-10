<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @license   https://github.com/MrPing/sallyphp#license
*/

namespace sally;

/**
 * Sally Acl
*/
class Acl
{
  /**
   * @var array
  */
  private $_role = array();
  private $_ressource = array();
  private $_privilage = array();

  /**
   * @var mixed
  */
  protected static $_instance = false;

  /**
   * Acl instance
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
   * Vérifie un privilège
   * @param string role name
   * @param string ressource name
   * @param string action name
   * @return array
  */
  private function checkPrivilage($role_name, $ressource_name, $action_name)
  {
    foreach ($this->_privilage as $row) {
      if ($row['role'] == $role_name && $row['ressource'] == $ressource_name) {
        if (count($row['action']) > 0) {
          if (in_array($action_name, $row['action'])) {
            return array(true, $row['allow']);
          } else {
            return array(false, false);
          }
        } else {
          return array(true, $row['allow']);
        }
        break;
      }
    }
    return array(false, false);
  }

  /**
   * Remonte les parents d'une ressource
   * @param array role parents
   * @param array ressource parents
   * @param string action name
   * @return boolean
  */
  private function goParents($role_parents, $ressource_parents, $action_name)
  {
    foreach ($ressource_parents as $ressource_parent_name) {
      foreach ($role_parents as $role_parent_name) {
        list($exist, $allow) = $this->checkPrivilage($role_parent_name, $ressource_parent_name, $action_name);
        if ($exist) {
          return $allow;
        }
      }
    }
    return false;
  }

  /**
   * Détermine si un role à le droit d'accéder à une ressource et une action
   * @param string role name
   * @param string ressource name
   * @param string action name
   * @return boolean
  */
  public function isAllowed($role_name, $ressource_name, $action_name)
  {
    if (!array_key_exists($role_name, $this->_role)) {
      throw new Exception('Le role "' . $role_name . '" n\'existe pas.');
    }

    if (!array_key_exists($ressource_name, $this->_ressource)) {
      throw new Exception('La ressource "' . $ressource_name . '" n\'existe pas.');
    }

    // tableau des role parents
    $role_parents = $this->_role[$role_name]['parents'];
    array_push($role_parents, $role_name);
    $role_parents = array_reverse($role_parents);

    // tableau des ressource parents
    $ressource_parents = $this->_ressource[$ressource_name]['parents'];
    array_push($ressource_parents, $ressource_name);
    $ressource_parents = array_reverse($ressource_parents);

    return $this->goParents($role_parents, $ressource_parents, $action_name);
  }

  /**
   * Ajouter un role
   * @param string role name
   * @param string parent name
  */
  public function role($name, $parent = null)
  {
    $parents = array();
    if ($parent != null) {
      $parents = array_merge($this->_role[$parent]['parents'], array($parent));
    }
    $this->_role[$name] = array(
      'parents' => $parents
    );
  }

  /**
   * Ajouter une ressource
   * @param string role name
   * @param string parent name
  */
  public function ressource($name, $parent = null)
  {
    $parents = array();
    if ($parent != null) {
      $parents = array_merge($this->_ressource[$parent]['parents'], array($parent));
    }
    $this->_ressource[$name] = array(
      'parents' => $parents
    );
  }

  /**
   * Interdire un role sur une ressource et une action
   * @param string role name
   * @param string ressource name
   * @param array action
  */
  public function deny($role_name, $ressource_name, $action = array())
  {
    array_push($this->_privilage, array(
      'role' => $role_name,
      'ressource' => $ressource_name,
      'action' => $action,
      'allow' => false
    ));
  }

  /**
   * Autoriser un role sur une ressource et une action
   * @param string role name
   * @param string parent name
   * @param array action
  */
  public function allow($role_name, $ressource_name, $action = array())
  {
    array_push($this->_privilage, array(
      'role' => $role_name,
      'ressource' => $ressource_name,
      'action' => $action,
      'allow' => true
    ));
  }
}