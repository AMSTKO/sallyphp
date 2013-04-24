<?php

include 'sallyphp/initializer.php';

$sally = Sally::getInstance();
$view = Sally_View::getInstance();

Sally::set('application', __DIR__ . '/application');
Sally::set('module.default', 'cli');

$sally->addModule('cli');
$view->disableControllerView();

echo $sally->init(isset($argv[1]) ? $argv[1] : '') . "\n";