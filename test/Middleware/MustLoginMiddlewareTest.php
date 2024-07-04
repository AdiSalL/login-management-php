<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Middleware;

use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\Session;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Service\SessionService;
use ProgrammerZamanNow\Belajar\PHP\MVC\Middleware\MustLoginMiddleWare;

class MustLoginMiddlewareTest extends TestCase {

    private MustLoginMiddelware $middleware;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
        

    public function setUp():void {
        $this->middleware = new MustLoginMiddleWare();
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
        

        putenv("mode=test");
    }

    public function testBeforeGuest() {
        $this->middleware->before();
        $this->expectOutpuRegex("[Location: /users/login]");
    }

    
    public function testBeforeLoginUser() {
        $user = new User();
        $user->id = uniqid();
        $user->name = "adi";
        $user->password = "adi";
        $this->userRepository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->userId = $user->id;
        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
    

        $this->middleware->before();
        $this->expectOutpuString("");
    }


}
