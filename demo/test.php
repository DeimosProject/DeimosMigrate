<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$helper = new \Deimos\Helper\Helper();
$slice  = new \Deimos\Slice\Slice($helper, [
    'adapter'  => 'mysql',
    //    'host'     => 'localhost', // optional
    //    'port'     => 3306, // optional
    'database' => 'test',
    'username' => 'root',
    'password' => 'root'
]);

$database = new \Deimos\Database\Database($slice);
$orm      = new \Deimos\ORM\ORM($helper, $database);
$migrate  = new \Deimos\Migrate\Migrate($orm);

$migrate->setPath(dirname(__DIR__) . '/sql');

var_dump($migrate->run());
