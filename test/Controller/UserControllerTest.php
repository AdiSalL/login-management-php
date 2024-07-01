<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\App {
    function header(string $value) {
        echo $value;
    }
}

namespace ProgrammerZamanNow\Belajar\MVC\Controller {
    
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Controller\UserController;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;


use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase {
    private UserController $userController;
    private UserRepository $userRepository;

    public function setUp():void {
        $this->userController = new UserController();
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

}

};

