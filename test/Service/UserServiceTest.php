<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Service;

use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Service\UserService;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserRegisterRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Exception\ValidationException;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserLoginRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserProfileUpdateResponse;

class UserServiceTest extends TestCase {
    private UserService $userService;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp():void {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);
        $this->sessionRepository = new SessionRepository($connection);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
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

        $this->expectException(ValidationException::class);
        $request = new UserRegisterRequest();
        $request->id = "";
        $request->name = "";
        $request->password = "";

        $this->userService->register($request);
        
    }

    
    public function testRegisterDuplicate() {   
        $user = new User();
        $user->id = "zain";
        $user->name = "zain";
        $user->password = "rahasia";


        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "zain";
        $request->name = "zain";
        $request->password = "rahasia";

        $response = $this->userService->register($request);
    }

    public function testLoginNotFound() {
        $this->expectException(ValidationException::class);
        $request = new UserLoginRequest();
        $request->id = "adi";
        $request->password = "password";

        $this->userService->login($request);
    }

    public function testLoginWrongPasswor() {
        $user = new User();
        $user->id = "adi";
        $user->name = "adi";
        $user->password = password_hash("adi", PASSWORD_BCRYPT);
        $this->expectException(ValidationException::class);
        $request = new UserLoginRequest();
        $request->id = "adi";
        $request->password = "password";

        $this->userService->login($request);
    }

    public function testLoginSuccess() {
        $user = new User();
        $user->id = "adi";
        $user->name = "adi";
        $user->password = password_hash("adi", PASSWORD_BCRYPT);
        $this->expectException(ValidationException::class);
        $request = new UserLoginRequest();
        $request->id = "adi";
        $request->password = "password";

        $response = $this->userService->login($request);

        self::assertEquals($request->id, $response->user->id);
        self::assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testUpdateSuccess(){
        $user = new User();
        $user->id = "madang";
        $user->name = "madang";
        $user->password = password_hash("madang", PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserProfileUpdateRequest();
        $request->id = "madang";
        $request->name = "Ida";

        $this->userService->updateProfile($request);

        $result = $this->userRepository->findById($request->id);
        self::assertEquals( $request->name, $result->name);

    }

    public function testUpdateValidationError(){
        $this->expectException(ValidationException::class);
        $request = new UserProfileUpdateRequest();
        $request->id = "";
        $request->name = "";

        $this->userService->updateProfile($request);

    }

    public function testUpdateNotFound() {

        $this->expectException(ValidationException::class);
        $request = new UserProfileUpdateRequest();
        $request->id = "madang";
        $request->name = "Ida";

        $this->userService->updateProfile($request);


    }
}