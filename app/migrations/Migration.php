<?php

namespace App\Migrations;

use PDO;

abstract class Migration implements MigrationInterface
{
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    protected function execute($sql, $message)
    {
        $this->pdo->exec($sql);
        echo $message . PHP_EOL;
    }

    protected function dropIfExists($table)
    {
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $sql = "DROP TABLE IF EXISTS {$table}";
        $this->pdo->exec($sql);
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }
}