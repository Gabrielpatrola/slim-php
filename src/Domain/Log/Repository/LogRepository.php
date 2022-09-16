<?php
declare(strict_types=1);

namespace App\Domain\Log\Repository;

use App\Domain\Log\Log;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class LogRepository
{
    public function __construct(private EntityManager $em)
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function insert(Log $log): Log
    {
        $this->em->persist($log);
        $this->em->flush();
        return $log;
    }

    public function findLogsByUserId(string $valueProperty): array
    {
        return $this->em->getRepository(Log::class)->createQueryBuilder('l')
            ->andWhere('l.user = :val')
            ->setParameter('val', $valueProperty)
            ->select('l.result, l.date')
            ->orderBy('l.date','DESC')
            ->getQuery()
            ->getArrayResult();
    }
}
