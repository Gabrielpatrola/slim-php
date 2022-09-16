<?php

declare(strict_types=1);

namespace App\Domain\Log\Service;

use App\Domain\Log\Log;
use App\Domain\Log\Repository\LogRepository;
use App\Domain\User\Repository\UserRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;

class LogCreate
{

    public function __construct(private LogRepository $repository, private UserRepository $userRepository)
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws Exception
     */
    public function createLog($log, $userId): Log
    {
        $user = $this->userRepository->findBy('id', $userId);
        $log = new Log($log, $user);
        return $this->repository->insert($log);
    }

}
