<?php
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../src/core/Router.php';

session_start();
error_reporting(E_ALL);
ini_set('display_errors', '0');

(Dotenv\Dotenv::createImmutable(__DIR__.'/../'))->load();

$router = new Router();

$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Auth', 'action' => 'login']);
$router->add('agendamento', ['controller' => 'Agendamento', 'action' => 'create']);

$router->dispatch($_SERVER['QUERY_STRING']);
