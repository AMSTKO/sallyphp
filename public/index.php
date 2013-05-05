<?php
include '../sallyphp/Sally.php';

$sally = Sally::getInstance();
$trafficker = Trafficker::getInstance();

Sally::set('application', __DIR__ . '/../application');
Sally::set('module.default', 'site');

if (getenv('environnement') == 'local') {
  Sally::set('path', '/sallyphp/index.php/');
  Sally::set('static', '/sally/static/');
}

$sally->addModule('site');
$sally->addModule('admin');
$trafficker->add('my');

echo $sally->init($_SERVER['REQUEST_URI']);