<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @phpstan-extends ServiceEntityRepository<User>
 */
final class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneOrNullByUsername(string $username): ?User
    {
        return $this->findOneBy([
            'username' => $username,
        ]);
    }

    public function findOneOrNullByEmail(string $email): ?User
    {
        return $this->findOneBy([
            'email' => $email,
        ]);
    }

    public function findOneOrNullByForgotPasswordToken(string $token): ?User
    {
        return $this->findOneBy([
            'resetPasswordToken' => $token,
        ]);
    }
}
