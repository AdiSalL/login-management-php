<?php


namespace ProgrammerZamanNow\Belajar\PHP\MVC\Service;


use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\Session;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;


use PHPUnit\Framework\TestCase;

function setcookie(string $name, string $value) {
    echo "[$name : $value]";
}

class SessionServiceTest extends TestCase {
    private SessionService $sessionService;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;
    

    protected function setUp():void {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new userRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);
        
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = "adi";
        $user->name = "adi";
        $user->password = "rahasia";
        
        $this->userRepository->save($user);
    }
    public function testCreate() {

        $session = $this->sessionService->create("adi");
        
        $this->expectOutputRegex("[X-PZN-SESSION : $session->id]");

        $result = $this->sessionRepository->findById($session->id);
        self::assertEquals("adi", $result->userId);
    }

    public function testDestroy(){
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "adi";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->sessionService->destroy();
        $this->expectOutputRegex("[X-PZN-SESSION: ]");

        $result = $this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }

    public function testCurrent() {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "adi";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $user = $this->sessionService->current();
        self::assertEquals("adi", $user->id);
        

    }

    public function testCurrentNotFound() {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "adjawio";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $user = $this->sessionService->current();
        self::assertEquals("adi", $user->id);
        

    }



}
