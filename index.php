<?php

require __DIR__.'/vendor/autoload.php';

$config = include 'config.php';

use App\Requirements;

$requirements = new Requirements();
$phpReqs = $requirements->checkPHP();
$externalReqs = $requirements->checkExternalApps();
$databaseReqs = $requirements->checkDatabase($config['db']);

var_dump($phpReqs);
var_dump($externalReqs);
var_dump($databaseReqs);
die();

