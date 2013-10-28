<?php
/**
 * SallyPHP
 * Hello world!
*/

require '../sallyphp/Sally.php';

// set configuration variables
Sally::set('application', __DIR__ . '/../application');
Sally::set('module.default', 'site');

// add a database connection
/*$db = sally\Db::getInstance();
$db->add(array(
  'type' => 'mysql-pdo',
  'name' => 'default',
  'host' => '127.0.0.1',
  'dbname' => 'xyz',
  'user' => 'yxz',
  'passwd' => '***'
));*/

// get Sally instance
$sally = Sally::getInstance();

// add modules to application
$sally->module->add('site');

// prepare the request and get "Sally Engine object"
$engine = $sally->query->prepare($_SERVER['REQUEST_URI']);

// add a traffickers and helpers for the request
$engine->trafficker->add('my', array('site'));
$engine->helper->add('/toStrong');

// execute the request
echo $engine->execute();