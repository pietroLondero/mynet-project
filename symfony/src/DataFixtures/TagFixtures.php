<?php

namespace App\DataFixtures;


ini_set('time_limit', 0);

use App\Entity\Tag;
use App\Entity\Url;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Faker\Factory;
use Doctrine\ORM\EntityManagerInterface;

class TagFixtures extends Fixture
{
    private EntityManagerInterface $entityManager;
    private $faker;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->faker = Factory::create();
    }


    public function load(ObjectManager $manager): void
    {
        echo "seeding tags" . PHP_EOL;
        $this->generateTags($manager);
    }

    private function generateTags($manager)
    {
        for ($i = 0; $i < 200; $i++) {
            $tag = new Tag();
            $tag->setTag("tag_" . $i);

            $manager->persist($tag);
            $this->addReference('tag_' . $i, $tag);
        }

        $manager->flush();
        $manager->clear();
    }

    private function generateRandomString($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }
}
