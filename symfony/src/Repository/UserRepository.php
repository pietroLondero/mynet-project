<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getUserById(int $id): ?User
    {
        return $this->find($id);
    }

    public function userFollowsUser(User $user, User $followedUser): bool
    {
        return $user->getFollowing()->contains($followedUser);
    }

    public function follow(User $user, User $followedUser): bool
    {
        $user->follow($followedUser);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return true;
    }

    public function getUserFollowedUrls(User $user, int $offset, int $limit)
    {
        return $this->createQueryBuilder('u')
            ->select('uf.id', 'ur.url', 'ur.timeInsert')
            ->innerJoin('u.following', 'uf')
            ->innerJoin('uf.followers', 'uf1')
            ->innerJoin('uf1.urls', 'ur')
            ->where('u.id = :id')
            ->setParameter('id', $user->getId())
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countUserFollowedUrls(User $user): int
    {
        return $this->createQueryBuilder('u')
            ->select('count(ur.id)')
            ->innerJoin('u.following', 'uf')
            ->innerJoin('uf.followers', 'uf1')
            ->innerJoin('uf1.urls', 'ur')
            ->where('u.id = :id')
            ->setParameter('id', $user->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getFollowers(User $user): Collection
    {
        return $user->getFollowers();
    }
}
