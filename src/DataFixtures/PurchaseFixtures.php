<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\UserFixtures;
use App\Entity\Purchase;

class PurchaseFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $purchase = new Purchase();

        $purchase->setUser($this->getReference("user.1"));
        $purchase->setCreatedAt(new \DateTime());
        $purchase->setPrice(200);

        $this->addReference('purchase', $purchase);

        $manager->persist($purchase);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class
        );
    }
}
