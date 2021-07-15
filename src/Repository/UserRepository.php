<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    private const EMAIL_KEY = 'email';
    private const USERNAME_KEY = 'username';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByField(array $parameters): array
    {
        $emailSearch = array_key_exists(self::EMAIL_KEY, $parameters) ? $parameters[self::EMAIL_KEY] : '';
        $usernameSearch = array_key_exists(self::USERNAME_KEY, $parameters) ? $parameters[self::USERNAME_KEY] : '';
        $qb = $this->createQueryBuilder('u');

        $qb->where($qb->expr()->andX(
            $qb->expr()->like('u.email', ':email'),
            $qb->expr()->like('u.username', ':username'),
        ))
        ->setParameter('email', '%' . $emailSearch . '%')
        ->setParameter('username', '%' . $usernameSearch . '%');

        return $qb->getQuery()->getResult();
    }
}
