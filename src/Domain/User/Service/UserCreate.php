<?php

declare(strict_types=1);

namespace App\Domain\User\Service;

use App\Domain\User\Repository\UserRepository;
use App\Domain\User\User;
use App\Exception\ValidationException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;

class UserCreate
{

    public function __construct(private UserRepository $repository)
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws Exception
     */
    public function createUser($user): int
    {
        $this->validateUser($user);
        $user = new User($user['name'], $user['email'], $user['password']);
        return $this->repository->insert($user);
    }

    /**
     * @throws Exception
     */
    private function validateUser($user): void
    {
        $errors = null;

        if (empty($user['name'])) {
            $errors['name'] = 'Input required';
        }

        if (empty($user['password'])) {
            $errors['password'] = 'Input required';
        }
        if (is_null($user['email'])) {
            $errors['email'] = 'Input required';
        } elseif (filter_var($user['email'], FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'Invalid email address';
        }

        if ($this->repository->findBy('email', $user['email']) !== null) {
            $errors['email'] = 'Email address already used';
        }
        if ($errors) {
            throw new ValidationException('Please check your input', $errors, 422);
        }
    }
}
