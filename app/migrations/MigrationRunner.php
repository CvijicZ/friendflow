<?php

namespace App\Migrations;

use App\Migrations\MigrationInterface;
use PDO;

class MigrationRunner
{
    protected $pdo;
    protected $migrations = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->migrations = [
            new CreateUsersTable($pdo),
            new CreatePostsTable($pdo),
            new CreateCommentsTable($pdo),
            new CreateFriendRequestsTable($pdo),
            new CreateFriendsTable($pdo),
            new CreateMessagesTable($pdo)
        ];
    }

    public function migrate()
    {
        foreach ($this->migrations as $migration) {
            $migration->up();
        }
    }

    public function rollback()
    {
        foreach (array_reverse($this->migrations) as $migration) {
            $migration->down();
        }
    }
}