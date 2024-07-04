<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Service {
    function setcookie(string $name, string $value) {
        echo "[$name : $value]";
    }
}

namespace ProgrammerZamanNow\Belajar\PHP\MVC\App {
    function header(string $value) {
        echo $value;
    }
}


namespace ProgrammerZamanNow\Belajar\PHP\MVC\Controller {
    use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Service\SessionService;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\Session;


use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase {

    private HomeController $homeController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    

    public function setUp():void {
        $this->homeController = new HomeController();
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
        
    }

    public function testGuest() {
        $this->homeController->index();
        $this->expectOutputRegEx("[Login Management]");

    }   

    public function testUserLogin() {
        $user = new User();
        $user->id = "Ida";
        $user->name = "Ida";
        $user->password = "Ida";

        $this->userRepository->save($user);
        $session = new Session();
        $session->id = uniqid();
        $session->userId = $user->id;
        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->homeController->index();
        $this->expectOutputRegEx("[Hello, Ida Welcome to Dashboard]");
        $this->expectOutputRegEx("[Ida]");
        

    }




}
};