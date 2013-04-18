<?php

include 'sallyphp/initializer.php';

$sally = Sally::getInstance();
$db = Sally_Db::getInstance();
$view = Sally_View::getInstance();

Sally::set('application', __DIR__ . '/application');
Sally::set('module.default', 'cli');
Sally::set('rijndael.key', 'a5f8yu9i4d5z4h6w6q4m7d2b6gt4z96');

$sally->addModule('cli');

// hide view
$view->_display = false;

// set one databases
/*$db->add(array(
  'type' => 'mysql_pdo',
  'name' => 'my local db',
  'host' => '127.0.0.1',
  'dbname' => 'mydbname',
  'user' => 'myuser',
  'passwd' => 'mypasswd'
));*/


echo $sally->init(isset($argv[1]) ? $argv[1] : '') . "\n";