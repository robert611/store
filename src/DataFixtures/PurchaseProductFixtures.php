<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\PurchaseProduct;
use App\DataFixtures\PurchaseFixtures;

class PurchaseProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $purchaseProduct = new PurchaseProduct();

        $product = $this->getReference('product.0');

        $purchaseProduct->setPurchase($this->getReference('purchase'));
        $purchaseProduct->setQuantity(2);
        $purchaseProduct->setProduct($product);
        $purchaseProduct->setDeliveryType($product->getDeliveryTypes()[0]);
        $purchaseProduct->setIsPaid(0);

        $this->addReference('purchaseProduct.0', $purchaseProduct);

        $purchaseProductWithCashPayment = new PurchaseProduct();

        $product = $this->getReference('product.1');

        $purchaseProductWithCashPayment->setPurchase($this->getReference('purchase'));
        $purchaseProductWithCashPayment->setQuantity(1);
        $purchaseProductWithCashPayment->setProduct($product);
        $purchaseProductWithCashPayment->setDeliveryType($product->getDeliveryTypes()[2]);
        $purchaseProductWithCashPayment->setIsPaid(2);

        $this->addReference('purchaseProduct.1', $purchaseProductWithCashPayment);

        $manager->persist($purchaseProduct);
        $manager->persist($purchaseProductWithCashPayment);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            PurchaseFixtures::class
        );
    }
}
