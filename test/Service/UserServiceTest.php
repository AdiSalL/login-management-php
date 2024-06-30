<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Service;

use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Service\UserService;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserRegisterRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;

class UserServiceTest extends TestCase {
    private UserService $userService;

    protected function setUp():void {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);

        $userRepository->deleteAll();
    }

    public function testRegisterSuccess() {
        $request = new UserRegisterRequest();
        $request->id = "zain";
        $request->name = "zain";
        $request->password = "rahasia";

        $response = $this->userService->register($request);
        $this->assertEquals($request->id, $response->user->id);
        $this->assertEquals($request->name, $response->user->name);
        $this->assertNotEquals($request->password, $response->user->password);

        $this->assertTrue(password_verify($request->password, $response->user->password));

        
    }

    public function testRegisterFailed() {
        
    }

    
    public function testRegisterDuplicate() {
        
    }

}