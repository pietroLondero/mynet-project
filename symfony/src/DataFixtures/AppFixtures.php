<?php

namespace App\DataFixtures;


ini_set('time_limit', 0);

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

class AppFixtures extends Fixture
{

    private $userSeedNumber;

    public function __construct(int $userSeedNumber)
    {
        $this->userSeedNumber = $userSeedNumber;
    }



    public function load(ObjectManager $manager): void
    {
        if (!isset($this->userSeedNumber) || $this->userSeedNumber < 1) {
            throw new \Exception("User seed number must be defined and greater than 0");
        }
    }
}
