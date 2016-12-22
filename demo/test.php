<?php

include_once __DIR__ . '/../vendor/autoload.php';

\Deimos\ORM\Connection::setConfig([
    'default' => [
        'dsn'      => 'mysql:host=localhost;dbname=test111',
        'username' => 'root',
        'password' => ''
    ],
]);

$builder = new \Deimos\ORM\Builder();

$migrate = new \Deimos\Migrate\Migrate($builder);

$migrate->setPath(dirname(__DIR__) . '/sql');

var_dump($migrate->run());