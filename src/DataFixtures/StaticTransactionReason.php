<?php

namespace App\DataFixtures;

use App\Entity\StaticTransactionReason as Entity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StaticTransactionReason extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $entity = new Entity();
        $entity->setName('stock');
        $manager->persist($entity);

        $entity = new Entity();
        $entity->setName('refund');
        $manager->persist($entity);

        $manager->flush();
    }
}
