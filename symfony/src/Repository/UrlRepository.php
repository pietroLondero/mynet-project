<?php

namespace App\Repository;

namespace App\Repository;

use App\Entity\Url;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UrlRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Url::class);
    }

    public function getByTimeInsert(int $offset, int $limit)
    {
        $qb = $this->createQueryBuilder('u');

        $qb->select('u.id', 'u.url', 'u.timeInsert', 'us.username')
            ->innerJoin('u.tags', 't')
            ->innerJoin('u.user', 'us')
            ->orderBy('u.timeInsert', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function countAll()
    {
        $qb = $this->createQueryBuilder('u');

        $qb->select('count(u.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findByTag(string $tag, int $offset, int $limit): array
    {

        $qb = $this->createQueryBuilder('u');

        $qb->select('u.id', 'u.url', 'u.timeInsert', 'us.username')
            ->innerJoin('u.tags', 't')
            ->innerJoin('u.user', 'us')
            ->where('t.tag = :tag')
            ->setParameter('tag', $tag)
            ->orderBy('u.timeInsert', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);


        return $qb->getQuery()->getResult();
    }

    public function countByTag(string $tag): int
    {
        $qb = $this->createQueryBuilder('u');

        $qb->select('count(u.id)')
            ->innerJoin('u.tags', 't')
            ->where('t.tag = :tag')
            ->setParameter('tag', $tag);

        return $qb->getQuery()->getSingleScalarResult();
    }


    public function getUrlById(int $urlId)
    {
        return $this->find($urlId);
    }

    public function userLikesUrl(User $user, int $urlId)
    {
        $qb = $this->createQueryBuilder('u');

        $qb->select('u.id, us.id')
            ->innerJoin('u.likedBy', 'us')
            ->where('u.id = :postId')
            ->andWhere('us.id = :userId')
            ->setParameter('postId', $urlId)
            ->setParameter('userId', $user->getId());

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function likeUrl(User $user, int $urlId)
    {
        $url = $this->find($urlId);

        $url->addLikedBy($user);

        $this->getEntityManager()->persist($url);
        $this->getEntityManager()->flush();
    }

    public function addUrl(User $user, array $tags)
    {
        $url = new Url();
        $url->setUrl($this->generateRandomString());
        $url->setTimeInsert(time());
        $url->setUser($user);

        foreach ($tags as $tag) {
            $url->addTag($tag);
        }

        $this->getEntityManager()->persist($url);
        $this->getEntityManager()->flush();
        return $url;
    }


    public function getUserUrl(int $urlId)
    {
        $qb = $this->createQueryBuilder('u');

        $qb->select('us.id', 'us.email', 'us.username')
            ->innerJoin('u.user', 'us')
            ->where('u.id = :id')
            ->setParameter('id', $urlId);

        return $qb->getQuery()->getResult();
    }

    private function generateRandomString($length = 10): string
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }
}
