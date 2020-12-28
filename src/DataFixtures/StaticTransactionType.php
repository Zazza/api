<?php

namespace App\DataFixtures;

use App\Entity\StaticTransactionType as Entity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StaticTransactionType extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $entity = new Entity();
        $entity->setName('debit');
        $manager->persist($entity);

        $entity = new Entity();
        $entity->setName('credit');
        $manager->persist($entity);

        $manager->flush();
    }
}
