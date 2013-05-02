<?php
/**
 * SallyPHP
 *
 * @link      https://github.com/MrPing/sallyphp
 * @copyright Copyright (c) 2013, Jonathan Amsellem.
 * @license   https://github.com/MrPing/sallyphp#license
 */

class Sally_Acl
{
  private $_role = array();
  private $_ressource = array();
  private $_privilage = array();
  protected static $_instance = false;

  public static function getInstance()
  {
    if (!self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

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

  public function isAllowed($role_name, $ressource_name, $action_name)
  {
    if (!array_key_exists($role_name, $this->_role)) {
      throw new Exception('Le role "' . $role_name . '" n\'existe pas.');
    }

    if (!array_key_exists($ressource_name, $this->_ressource)) {
      throw new Exception('La ressource "' . $ressource_name . '" n\'existe pas.');
    }

    $role_parents = $this->_role[$role_name]['parents'];
    array_push($role_parents, $role_name);
    $role_parents = array_reverse($role_parents);

    $ressource_parents = $this->_ressource[$ressource_name]['parents'];
    array_push($ressource_parents, $ressource_name);
    $ressource_parents = array_reverse($ressource_parents);

    return $this->goParents($role_parents, $ressource_parents, $action_name);
  }

  public function addRole($name, $parent = null)
  {
    $parents = array();
    if ($parent != null) {
      $parents = array_merge($this->_role[$parent]['parents'], array($parent));
    }
    $this->_role[$name] = array(
      'parents' => $parents
    );
  }

  public function addRessource($name, $parent = null)
  {
    $parents = array();
    if ($parent != null) {
      $parents = array_merge($this->_ressource[$parent]['parents'], array($parent));
    }
    $this->_ressource[$name] = array(
      'parents' => $parents
    );
  }

  public function deny($role_name, $ressource_name, $action = array())
  {
    array_push($this->_privilage, array(
      'role' => $role_name,
      'ressource' => $ressource_name,
      'action' => $action,
      'allow' => false
    ));
  }

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