<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Product;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\CategoryFixtures;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $product = new Product();
        $product->setName('Priceless widget');
        $product->setPrice(14.50);
        $product->setDescription('Ok, I guess it *does* have a price');
        $product->setCategory($this->getReference("category.1"));
        $product->setState('nowy');
        $product->setAuctionType('auction');
        $product->setDeliveryTime('72');
        $product->setOwner($this->getReference("user.0"));
        $product->setCreatedAt(new \DateTime());

        $manager->persist($product);

        $manager->flush();

        $this->addReference('product', $product);
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            CategoryFixtures::class
        );
    }
}
