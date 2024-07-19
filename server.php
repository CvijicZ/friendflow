<?php
require __DIR__.'/vendor/autoload.php';

use App\Services\Chat;

$server = new \Ratchet\App('192.168.1.9', 8080, '192.168.1.9');
$server->route('/chat', new Chat, ['*']);
$server->run();
