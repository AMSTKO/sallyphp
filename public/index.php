<?php

include '../sallyphp/Sally.php';

$sally = Sally::getInstance();

Sally::set('application', __DIR__ . '/../application');
Sally::set('module.default', 'site');

$sally->addModule('site');
$sally->addModule('admin');

echo $sally->load($_SERVER['REQUEST_URI'], array('my'));
//echo $sally->init('/index/request');