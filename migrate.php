<?php

require __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

use App\Migrations\MigrateCommand;

MigrateCommand::run();