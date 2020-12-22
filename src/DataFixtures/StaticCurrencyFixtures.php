<?php

namespace App\DataFixtures;

use App\Entity\StaticCurrency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StaticCurrencyFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $currency = new StaticCurrency();
        $currency->setName('RUB');
        $manager->persist($currency);

        $currency = new StaticCurrency();
        $currency->setName('USD');
        $manager->persist($currency);

        $manager->flush();
    }
}
