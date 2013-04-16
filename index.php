<?php
include 'sallyphp-1.130309.1/initializer.php';

$sally = Sally::getInstance();
$db = Sally_Db::getInstance();
$acl = Sally_Acl::getInstance();
$trafficker = Sally_Trafficker::getInstance();
$PHPMailer = Sally_PHPMailer::getInstance();

Sally::set('application', '/home/jonathan/Documents/projects/outirl.com/application');
Sally::set('name', 'Outirl');
Sally::set('module.default', 'site');
Sally::set('rijndael.key', 'pass');
//Sally::set('cookie.domain', '.outirl.local');
Sally::set('cookie.name', 'outirl');
Sally::set('cookie.iv', '123456');

if (getenv('environnement') == 'ip') {
  Sally::set('path', '/outirl/');
  Sally::set('static', '/outirl/static/');
} else {
  Sally::set('path', '/');
  Sally::set('static', '/static/');
}

$sally->addModule('site');
$sally->addModule('admin');

$trafficker->add('outirl');

$db->add(array(
  'type' => 'mysql_pdo',
  'host' => '127.0.0.1',
  'dbname' => 'outirl',
  'user' => 'outirl',
  'passwd' => 'password'
));

$PHPMailer->IsSMTP();
$PHPMailer->Host = 'in.mailjet.com';
$PHPMailer->SMTPAuth = true;
$PHPMailer->Username = 'username';
$PHPMailer->Password = 'password';
$PHPMailer->SMTPSecure = 'tls'; 

$acl->addRole('guest');
$acl->addRole('admin', 'guest');
$acl->AddRessource('public');
$acl->AddRessource('admin');
$acl->AddRessource('admin_home', 'admin');
$acl->allow('guest', 'public');
$acl->allow('guest', 'admin_index', array('index', 'error'));
$acl->allow('admin', 'admin');

echo $sally->page();