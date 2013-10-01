<?php

include '../sallyphp/Sally.php';

$sally = Sally::getInstance();

Sally::set('application', __DIR__ . '/../application');
Sally::set('module.default', 'site');

$sally->module->add('site');
$sally->module->add('admin');

$engine = $sally->prepare($_SERVER['REQUEST_URI']);
$engine->trafficker->add('my');
echo $engine->execute();