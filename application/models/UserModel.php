<?php
class UserModel extends Model
{
  public function getEmail()
  {
    $db = Db::getConnection();
    $stmt = $db->prepare('SELECT email FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(array('id' => 1));
    $result = $stmt->fetch();
    return $result['email'];
  }
}