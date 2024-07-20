<?php
require __DIR__.'/vendor/autoload.php';

use App\Services\Chat;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$server = new \Ratchet\App($_ENV['WS_HOST'], $_ENV['WS_PORT'], $_ENV['WS_IP']);
$server->route('/chat', new Chat, ['*']);
$server->run();
