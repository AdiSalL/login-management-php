<?php


namespace ProgrammerZamanNow\Belajar\PHP\MVC\Repository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;

class UserRepository {
    private \PDO $connection;

    public function __construct(\PDO $connection) {
        $this->connection = $connection;
    }

    public function save(User $user): User{
        $statement = $this->connection->prepare("INSERT INTO user (id, name, password) VALUES (?, ?, ?)");
        $statement->execute([
            $user->id, $user->name, $user->password
        ]);
        return $user;
    }

    public function findById(string $id): ?User {
        $statement = $this->connection->prepare("SELECT name, id, password FROM user WHERE id = ?");
        $statement->execute([$id]);

        try {
        if($row = $statement->fetch()) {
        $user = new User();
        $user->id = $row["id"];
        $user->name = $row["name"];
        $user->password = $row["password"];
        return $user;
        }else {
            return null;
        }
        }finally {
            $statement->closeCursor();
        }
    }

    public function update(User $user): User {
        $statement = $this->connection->prepare("UPDATE user SET name = ?, password = ? WHERE id = ?");
        $statement->execute([$user->name, $user->password, $user->id]);

        return $user;
    }


    public function deleteAll():void {
        $statement = $this->connection->prepare("DELETE FROM user");
        $statement->execute();
    }
}