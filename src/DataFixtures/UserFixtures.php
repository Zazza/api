<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setName('Ivan Petrov');
        $manager->persist($user);

        $user = new User();
        $user->setName('Petr Sidorov');
        $manager->persist($user);

        $user = new User();
        $user->setName('Sidor Ivanov');
        $manager->persist($user);

        $manager->flush();
    }
}
