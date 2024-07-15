<?php

namespace App\Migrations;

use App\Migrations\MigrationRunner;
use App\Migrations\CreateUsersTable;
use App\Core\Database;
use PDO;

class MigrateCommand
{
    public static function run()
    {
        $db=new Database();
        $pdo = $db->getConnection();
        $runner = new MigrationRunner($pdo);
        $runner->migrate();
    }
}