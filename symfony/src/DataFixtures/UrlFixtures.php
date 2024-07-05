<?php

namespace App\DataFixtures;

use App\Entity\Url;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\TagFixtures;

class UrlFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private int $userSeedNumber)
    {
        $this->userSeedNumber = $userSeedNumber;
    }

    public function load(ObjectManager $manager): void
    {
        echo "seeding urls" . PHP_EOL;
        $faker = Factory::create();
        $batchSize = 100;
        $urlInsertCounter = 0;

        // Preload references
        $users = [];
        for ($i = 0; $i < $this->userSeedNumber; $i++) {
            $users[] = $this->getReference('user_' . $i);
        }

        $tags = [];
        for ($i = 0; $i < 200; $i++) {
            $tags[] = $this->getReference('tag_' . $i);
        }


        foreach ($users as $user) {
            $urlsCount = rand(10, 20);
            for ($j = 0; $j < $urlsCount; $j++) {
                $url = new Url();
                $url->setUrl($this->generateRandomString(20));
                $url->setTimeInsert($faker->dateTimeBetween('-2 years', 'now')->getTimestamp());
                $url->setUser($user);

                // Assign tags
                $assignedTags = array_rand($tags, rand(1, 3));
                if (!is_array($assignedTags)) {
                    $assignedTags = [$assignedTags];
                }
                foreach ($assignedTags as $tagIndex) {
                    $tag = $tags[$tagIndex];
                    $url->addTag($tag);
                }
                $urls[] = $url;
                $urlInsertCounter++;

                if ($urlInsertCounter % $batchSize === 0) {
                    $manager->persist(...$urls);
                    $urls = [];
                    $manager->flush();
                }
            }
        }

        $manager->flush();
        $this->assignLikes($manager, $users);
        $manager->clear();
    }

    private function generateRandomString($length = 10): string
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    private function assignLikes(ObjectManager $manager, array $users): void
    {
        echo "seeding likes" . PHP_EOL;
        $urls = $manager->getRepository(Url::class)->findAll();
        foreach ($urls as $url) {
            $numberOfLikes = rand(0, 20);
            if ($numberOfLikes == 0) {
                continue;
            }

            $likingUsers = array_rand($users, $numberOfLikes);

            if (!is_array($likingUsers)) {
                $likingUsers = [$likingUsers];
            }

            foreach ($likingUsers as $userIndex) {
                $user = $users[$userIndex];
                $user->likeUrl($url);

                $manager->persist($user);
            }
        }

        $manager->flush();
    }


    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TagFixtures::class,
        ];
    }
}
