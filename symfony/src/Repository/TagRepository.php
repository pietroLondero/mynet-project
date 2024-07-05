<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function getTagsForUrl(int $id)
    {
        $qb = $this->createQueryBuilder('t');

        $qb->select('t.tag')
            ->innerJoin('t.urls', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $id);

        $tag = $qb->getQuery()->getResult();

        return array_map(fn ($tag) => $tag['tag'], $tag);
    }

    public function getTags(array $tags)
    {
        // array tags is an array of integers
        $tagsToReturn = [];
        $qb = $this->createQueryBuilder('t');

        $tagsToReturn = $qb->select()
            ->where('t.id IN (:ids)')
            ->setParameter('ids', $tags)
            ->getQuery()
            ->getResult();

        return $tagsToReturn;
    }
}
