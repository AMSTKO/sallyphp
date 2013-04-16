<?php
class LolModel extends Sally_Model
{
  public function getEmail()
  {
    $db = Sally_Db::getConnection('my local db');
    $stmt = $db->prepare('SELECT email FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(array('id' => 1));
    $result = $stmt->fetch();
    return $result['email'];
  }

  public function sayLol()
  {
    return 'lol';
  }
}