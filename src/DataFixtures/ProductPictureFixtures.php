<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\ProductPicture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\ProductFixtures;

class ProductPictureFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $pictures = $this->getPictures();

        foreach ($pictures as $key => $picture) {
            $productPicture = new ProductPicture();

            $productPicture->setName($picture['name']);
            $productPicture->setProduct($this->getReference("product." . ($picture['product_id'] - 1)));
            
            $manager->persist($productPicture);

            $this->addReference('productPicture.'. $key, $productPicture);
        }

        
        $manager->flush();
    }

    private function getPictures()
    {
        return [
            ['name' => 'test_picture1.png', 'product_id' => 1],
            ['name' => 'test_picture2.png', 'product_id' => 1],
            ['name' => 'test_picture3.png', 'product_id' => 2],
            ['name' => 'test_picture4.png', 'product_id' => 2],
            ['name' => 'test_picture5.png', 'product_id' => 3],
            ['name' => 'test_picture6.png', 'product_id' => 3],
            ['name' => 'test_picture7.png', 'product_id' => 4],
            ['name' => 'test_picture8.png', 'product_id' => 4],
            ['name' => 'test_picture9.png', 'product_id' => 5],
            ['name' => 'test_picture10.png', 'product_id' => 5],
            ['name' => 'test_picture11.png', 'product_id' => 6],
            ['name' => 'test_picture12.png', 'product_id' => 6]
        ];
    }

    public function getDependencies()
    {
        return array(
            ProductFixtures::class
        );
    }
}
