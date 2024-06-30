<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Repository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;

use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase {
    private UserRepository $userRepository;

    protected function setUp():void {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
    }

    public function testSaveSuccess() {
        $user = new User();
        $user->id = "udin";
        $user->name = "Udin";
        $user->password = "rahasia";
        
        $this->userRepository->save($user);
        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
        
    }

    public function testFindByIdNotFound() {
        $result = $this->userRepository->findById("notfound");
        self::assertNull($result);
    }


}