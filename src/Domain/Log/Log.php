<?php
declare(strict_types=1);

namespace App\Domain\Log;

use App\Domain\User\User;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\PostLoad;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity, Table(name: 'logs'), HasLifecycleCallbacks]
class Log
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    public int $id;
    #[Column(type: 'json', nullable: false)]
    public string $result;
    #[Column(type: 'datetimetz', nullable: false)]
    public \DateTime|string $date;
    #[ManyToOne(targetEntity: User::class)]
    public User $user;

    public function __construct(string $result, User $user,)
    {
        $this->result = $result;
        $this->user = $user;
    }

    /**
     * @param string $result
     */
    public function setResult(string $result): void
    {
        $this->result = $result;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    #[PrePersist]
    public function setDate()
    {
        return $this->date = new \DateTime('now');
    }
}
