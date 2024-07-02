<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ProgrammerZamanNow\Belajar\PHP\MVC\App\Router;
use ProgrammerZamanNow\Belajar\PHP\MVC\Controller\HomeController;
use ProgrammerZamanNow\Belajar\PHP\MVC\Controller\UserController;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;

Database::getConnection("prod");


Router::add('GET', '/', HomeController::class, 'index', []);

Router::add("GET", "/users/register", UserController::class, "register",  []);
Router::add("POST", "/users/register", UserController::class, "postRegister",  []);

Router::add("GET", "/users/login", UserController::class, "login", []);
Router::add("POST", "/users/login", UserController::class, "postLogin", []);


Router::run();