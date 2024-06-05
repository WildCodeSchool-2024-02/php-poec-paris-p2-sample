<?php

namespace App\Model;

use PDO;

class UserManager extends AbstractManager
{
    public const TABLE = 'user';

    public function insert(array $user): int|false
    {
        $query = 'INSERT INTO user (firstname, lastname, pseudo, password, avatar) ';
        $query .= 'VALUES (:firstname, :lastname, :pseudo, :password, :avatar);';

        $statement = $this->pdo->prepare($query);

        $statement->bindValue(':firstname', $user['firstname'], PDO::PARAM_STR);
        $statement->bindValue(':lastname', $user['lastname'], PDO::PARAM_STR);
        $statement->bindValue(':pseudo', $user['pseudo'], PDO::PARAM_STR);
        $statement->bindValue(':password', $user['password'], PDO::PARAM_STR);
        $statement->bindValue(':avatar', $user['avatar'], PDO::PARAM_STR);

        $statement->execute();

        return $this->pdo->lastInsertId();
    }

    public function selectOneByPseudo(string $pseudo): array|false
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE pseudo=:pseudo");
        $statement->bindValue('pseudo', $pseudo, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch();
    }
}
