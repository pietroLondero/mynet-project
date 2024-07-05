<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

class UserFixtures extends Fixture
{
    public function __construct(private int $userSeedNumber)
    {
        $this->userSeedNumber = $userSeedNumber;
    }


    public function load(ObjectManager $manager): void
    {
        echo "seeding users" . PHP_EOL;
        $password = password_hash('password', PASSWORD_BCRYPT);
        $users = [];
        for ($i = 0; $i < $this->userSeedNumber; $i++) {
            $user = new User();
            $user->setUsername("user_" . $i);
            $user->setEmail("user_" . $i . "@gmail.com");
            $user->setPassword($password);
            $this->addReference('user_' . $i, $user);
            $users[] = $user;
        }

        foreach ($users as $index => $user) {
            $manager->persist($user);
            if ($index % 100 == 0) {
                $manager->flush();
            }
        }

        $manager->flush();
        foreach ($users as $index => $user) {

            for ($j = 0; $j < rand(0, 10); $j++) {
                $followUser = $users[array_rand($users)];
                if ($followUser !== $user) {
                    $user->follow($followUser);
                }
            }
            $users[] = $user;
            if ($index % 100 == 0) {
                $manager->persist(...$users);
            }
        }

        $manager->flush();
        $manager->clear();
    }
}
