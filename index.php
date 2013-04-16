<?php
include 'sallyphp/initializer.php';

$sally = Sally::getInstance();
$db = Sally_Db::getInstance();
$acl = Sally_Acl::getInstance();
$trafficker = Sally_Trafficker::getInstance();
$PHPMailer = Sally_PHPMailer::getInstance();

// set configuration
Sally::set('application', '/var/www/sallyphp/application');
Sally::set('module.default', 'site');
Sally::set('rijndael.key', 'define a key!');
Sally::set('cookie.name', 'myproject');
Sally::set('cookie.iv', '123456');

if (getenv('environnement') == 'local') {
  Sally::set('path', '/sallyphp/');
  Sally::set('static', '/sallyphp/static/');
}

// set modules for HMVC
$sally->addModule('site');
$sally->addModule('admin');

// set traffickers
$trafficker->add('myplugin');

// set one databases
/*$db->add(array(
  'type' => 'mysql_pdo',
  'name' => 'my local db',
  'host' => '127.0.0.1',
  'dbname' => 'mydbname',
  'user' => 'myuser',
  'passwd' => 'mypasswd'
));*/

// set SMTP server
$PHPMailer->IsSMTP();
$PHPMailer->Host = 'in.mailjet.com';
$PHPMailer->SMTPAuth = true;
$PHPMailer->Username = 'username';
$PHPMailer->Password = 'password';
$PHPMailer->SMTPSecure = 'tls'; 

// set ACL (access-control-list)
$acl->addRole('guest');
$acl->addRole('admin', 'guest');
$acl->AddRessource('public');
$acl->AddRessource('admin');
$acl->AddRessource('admin_home', 'admin');
$acl->allow('guest', 'public');
$acl->allow('guest', 'admin_index', array('index', 'error'));
$acl->allow('admin', 'admin');

// display the page
echo $sally->page();