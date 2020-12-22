<?php

namespace App\DataFixtures;

use App\Entity\StaticCurrency;
use App\Entity\User;
use App\Entity\Wallet;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WalletFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $users = $manager
            ->getRepository(User::class)
            ->findAll();
        $currencies = $manager
            ->getRepository(StaticCurrency::class)
            ->findAll();

        foreach ($users as $user) {
            $wallet = new Wallet();
            $wallet->setUser($user);
            $currency = $currencies[random_int(0, count($currencies)-1)];
            $wallet->setCurrency($currency);
            $wallet->setAmount(0);
            $manager->persist($wallet);
        }

        $manager->flush();
    }
}
