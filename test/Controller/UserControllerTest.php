<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\App {
    function header(string $value) {
        echo $value;
    }
}

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Service {
    function setcookie(string $name, string $value) {
        echo "[$name : $value]";
    }
}

namespace ProgrammerZamanNow\Belajar\MVC\Controller {
    
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Controller\UserController;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Service\SessionService;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\Session;


use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase {
    private UserController $userController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;
    
    public function setUp():void {
        $this->userController = new UserController();

        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();

        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();

        putenv("mode=test");
    }

    public function testRegister(){
        $this->userController->register();
        $this->expectOutputRegex("[Register new User]");
        $this->expectOutputRegex("[Register]");
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Name]");
        
    }

    public function testPostRegisterSuccess() {
        $_POST["id"] = "adi";
        $_POST["name"] = "adi";
        $_POST["password"] = "adi";

        $this->userController->postRegister();

        $this->expectOutputRegex("[Location: /users/login]");
    }

    public function testPostRegisterVialidationError() {
        $_POST["id"] = "";
        $_POST["name"] = "adi";
        $_POST["password"] = "adi";
        $this->userController->postRegister();

        $this->expectOutputRegex("[Register new User]");
        $this->expectOutputRegex("[Id, Name, Password Can't Be Blank]");
        $this->expectOutputRegex("[Register]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Name]");

        
    }

    public function testPostRegisterDuplicate() {
        $user = new User();
        $user->id = "adi";
        $user->name = "adi";
        $user->password = "adi";

        $this->userRepository->save($user);

        $_POST["id"] = "adi";
        $_POST["name"] = "adi";
        $_POST["password"] = "adi";
        $this->userController->postRegister();

        $this->expectOutputRegex("[Register new User]");
        $this->expectOutputRegex("[User Id already exists]");
        $this->expectOutputRegex("[Register]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Name]");
    }

    public function testLogin() {
        $this->userController->login();
        $this->expectOutputRegex("[Login User]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Password]");

    }

    public function testLoginSuccess() {
        $user = new User();
        $user->id = "adi";
        $user->name = "adi";
        $user->password = password_hash("adi", PASSWORD_BCRYPT);

        $this->userRepository->save($user);
        $_POST["id"] = "adi";
        $_POST["name"] = "adi";
        $_POST["password"] = "adi";
        
        $this->userController->postLogin();
        $this->expectOutputRegex("[Location: /]");

        
        // $this->assertSame(password_verify($this->user->password, $this->userRepository->password));
    }

    public function testLoginValidationError() {
        $_POST["id"] = "";
        $_POST["password"] = "";
        

        $this->userController->postLogin();
        $this->expectOutputRegex("[Login User]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[Id, Password Can't Be Blank]");    
    }

    
    public function testLoginUserNotFound() {
        $_POST["id"] = "notfound";
        $_POST["password"] = "notfound";
        

        $this->userController->postLogin();
        $this->expectOutputRegex("[Login User]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[Id Or Password Is Wrong]");    
    }

    
    public function testLoginWrongPassword() {
        $user = new User();
        $user->id = "adi";
        $user->name = "adi";
        $user->password = password_hash("salah", PASSWORD_BCRYPT);

        $_POST["id"] = "notfound";
        $_POST["password"] = "notfound";
        

        $this->userController->postLogin();
        $this->expectOutputRegex("[Login User]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[Id Or Password Is Wrong]");    

    }

    public function testLogout() {

        $user = new User();
        $user->id = "adi";
        $user->name = "adi";
        $user->password = password_hash("adi", PASSWORD_BCRYPT);

        $this->userRepository->save($user);
        $session = new Session();
        $session->id =  uniqid();
        $session->userId = $user->id;
        $this->sessionRepository->save($session);
        
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->userController->logout();

        $this->expectOutputRegex("[Location: /]");
        $this->expectOutputRegex("[X-PZN-SESSION: ]");
        
    }

}

};

