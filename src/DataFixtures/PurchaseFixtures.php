<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\UserFixtures;
use App\Model\PurchaseCodeGenerator;
use App\Entity\Purchase;

class PurchaseFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $purchase = new Purchase();

        $purchaseRepository = $manager->getRepository(Purchase::class);

        $code = (new PurchaseCodeGenerator($purchaseRepository))->generate();

        $purchase->setUser($this->getReference("user.1"));
        $purchase->setCreatedAt(new \DateTime());
        $purchase->setPrice(200);
        $purchase->setCode($code);

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
