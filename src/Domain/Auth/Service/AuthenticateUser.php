<?php

namespace App\Domain\Auth\Service;

use App\Domain\User\Repository\UserRepository;
use App\Domain\User\User;
use App\Exception\ValidationException;
use Exception;
use Firebase\JWT\JWT;

class AuthenticateUser
{
    public function __construct(private UserRepository $repository)
    {
    }

    /**
     * @throws Exception
     */
    public function validateUser($user)
    {
        $findUser = $this->repository->findBy('email', $user['email'] ?? 'email');
        $errors = null;

        if (is_null($findUser)) {
            $errors['input'] = 'Email or password wrong';
            throw new ValidationException('Please check your input', $errors, 422);
        }

        if (array_key_exists('password', $user) && !$findUser->validatePassword($user['password'])) {
            $errors['input'] = 'Email or password wrong';
            throw new ValidationException('Please check your input', $errors, 422);

        }

        return $this->createToken($findUser);
    }

    private function createToken(User $user)
    {
        $now = time();
        $future = strtotime('+1 hour', $now);

        return JWT::encode([
            'id' => $user->id,
            'username' => $user->name,
            'email' => $user->email,
            "iat" => $now,
            "exp" => $future
        ],
            $_ENV['JWT_SECRET'],
            'HS256');
    }

}
