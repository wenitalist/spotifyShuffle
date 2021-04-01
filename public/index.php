<?php
session_start();

require_once(__DIR__ . '/../vendor/autoload.php');

$router = new \App\Router();
$router->checkUrl();
