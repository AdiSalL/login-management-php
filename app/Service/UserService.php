<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Service;

use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserRegisterRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserRegisterResponse;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Exception\ValidationException;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserLoginRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserLoginResponse;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserProfileUpdateResponse;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserPasswordUpdateResponse;



class UserService {
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request):UserRegisterResponse{
        $this->validateUserRegistrationRequest($request);
        try {
        Database::beginTransaction();
        
        $user = $this->userRepository->findById($request->id);
        if($user != null) {
            throw new ValidationException("User Id already exists");
        }

        $user = new User();
        $user->id = $request->id;
        $user->name = $request->name;
        $user->password = password_hash($request->password , PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $response = new UserRegisterResponse();
        $response->user = $user;
        
        Database::commitTransaction();
        return $response;

        }catch(\Exception $exception) {
        Database::rollbackTransaction();
        throw $exception;
    }
        
    }

    public function validateUserRegistrationRequest(UserRegisterRequest $request) {
        if($request->id == null || $request->name == null  || $request->password == null || trim($request->id == "") ||  trim($request->name == "") ||  trim($request->password == "")) 
        {
            throw new ValidationException("Id, Name, Password Can't Be Blank");
        }
    }

    public function login(UserLoginRequest $request) {
        $this->validateUserLoginRequest($request);
        $user = $this->userRepository->findById($request->id);
        if($user == null) {
            throw new ValidationException("Id Or Password Is Wrong");
        }

        if(password_verify($request->password, $user->password)) {
            $response = new UserLoginResponse();
            $response->user = $user;
            return $response;

        }else {
            throw new ValidationException("Id Or Password Is Wrong");
        }
    }

    private function validateUserLoginRequest(UserLoginRequest $request) {
        if($request->id == null ||  $request->password == null ) 
        {
            throw new ValidationException("Id, Password Can't Be Blank");
        }
    }

    public function updateProfile(UserProfileUpdateRequest $request):UserProfileUpdateResponse {
        $this->validateUserProfileUpdateRequest($request);

        try {
            Database::beginTransaction();
            $user = $this->userRepository->findById($request->id);
            if($user == null) {
                throw new ValidationException("User Is Not Found");
            }
            $user->name = $request->name;
            $this->userRepository->update($user);

            Database::commitTransaction();
            $response = new UserProfileUpdateResponse();
            $response->user = $user;
            return $response;

        }catch(\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserProfileUpdateRequest(UserProfileUpdateRequest $request) {
        if($request->id == null ||  $request->name == null || trim($request->id == "") ||  trim($request->name == "") ) 
        {
            throw new ValidationException("Id, Name Can't Be Blank");
        }
    }

    public function updatePassword(UserPasswordUpdateRequest $request): UserPasswordUpdateResponse{
        $this->validateUserPasswordUpdateRequest($request);
        try {
            Database::beginTransaction();
            $user = $this->userRepository->findById($request->id);
            if($user == null) {
                throw new ValidationException("User in not found");
            }

            if(!password_verify($request->oldPassword, $user->password)){
                throw new ValidationException("Old Password Is Wrong");
            }

            if(password_verify($request->newPassword, $user->password)){
                throw new ValidationException("Old And New Password Cant Be Same");
            }

            $user->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
            $this->userRepository->update($user);
            Database::commitTransaction();
            $response = new UserPasswordUpdateResponse();
            $response->user = $user;
            return $response;

        }catch(\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    
    }

    private function validateUserPasswordUpdateRequest(UserPasswordUpdateRequest $request) {
        if($request->id == null ||  $request->oldPassword == null ||  $request->oldPassword == null) 
        {
            throw new ValidationException("Id, Old Password, New Password Can't Be Blank");
        }
    }
}