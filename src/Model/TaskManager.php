<?php

namespace App\Model;

use PDO;
use DateTime;

class TaskManager extends AbstractManager
{
    public const TABLE = 'task';
    public const PRIORITY_LOW = ['level' => 1, 'label' => 'Low'];
    public const PRIORITY_MEDIUM = ['level' => 2, 'label' => 'Middle'];
    public const PRIORITY_HIGH = ['level' => 3, 'label' => 'High'];
    public const PRIORITIES = [self::PRIORITY_LOW, self::PRIORITY_MEDIUM, self::PRIORITY_HIGH];

    public const STATUS_PENDING = 'pending';
    public const STATUS_DONE = 'done';
    public const STATUSES = [self::STATUS_PENDING, self::STATUS_DONE];

    /**
     * Insert new item in database
     */
    public function insert(array $task): int
    {
        $createdAt = new DateTime('now');

        $query = "INSERT INTO " . self::TABLE . " (`label`, `status`, `priority`, `created_at`) ";
        $query .= "VALUES (:label, '" . $task['status'] . "', :priority, '" . $createdAt->format('Y-m-d h:i:s') . "')";

        $statement = $this->pdo->prepare($query);
        $statement->bindValue(':label', $task['label'], PDO::PARAM_STR);
        $statement->bindValue(':priority', $task['priority'], PDO::PARAM_INT);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }
}
