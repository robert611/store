<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Product;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\DeliveryTypeFixtures;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $products = $this->getProducts();

        foreach ($products as $key => $data) {
            $product = new Product();
            $product->setName($data['name']);
            $product->setPrice($data['price']);
            $product->setDescription($data['description']);
            $product->setCategory($this->getReference("category." . $data['category']));
            $product->setState($data['state']);
            $product->setAuctionType($data['auction_type']);
            $product->setDeliveryTime($data['delivery_time']);
            $product->setOwner($this->getReference("user.0"));
            $product->setDuration($data['duration']);
            $product->setQuantity($data['quantity']);
            $product->setIsSoldOut($data['is_sold_out']);
            $product->setCreatedAt(new \DateTime());

            foreach ($data['delivery_type'] as $deliveryType) 
            {
                $product->addDeliveryType($this->getReference("deliveryType." . ($deliveryType - 1)));
            }
    
            $manager->persist($product);

            $this->addReference('product.'. $key, $product);
        }
        
        $manager->flush();

    }

    public function getProducts()
    {
        return [
            [
                'name' => 'Pralka', 
                'description' => 'Świetna pralka', 
                'price' => '1200',
                'category' => 1,
                'state' => "nowy",
                'auction_type' => 'auction',
                'delivery_time' => '72',
                'duration' => '62',
                'quantity' => '20',
                'is_sold_out' => '0',
                'delivery_type' => [
                    1, 2, 4
                ]
            ],
            [
                'name' => 'Odkurzacz',
                'description' => 'Świetny odkurzacz', 
                'price' => '385',
                'category' => 1,
                'state' => "używany",
                'auction_type' => 'buy_now',
                'delivery_time' => '72',
                'duration' => '21',
                'quantity' => '20',
                'is_sold_out' => '0',
                'delivery_type' => [
                    2, 3, 6
                ]
            ],
            [
                'name' => 'Telefon', 
                'description' => 'Świetny telefon', 
                'price' => '1900',
                'category' => 1,
                'state' => "nowy",
                'auction_type' => 'auction',
                'delivery_time' => '48',
                'duration' => '0',
                'quantity' => '0',
                'is_sold_out' => '1',
                'delivery_type' => [
                    4, 5, 6
                ]
            ],
            [
                'name' => 'Obraz na płótnie',
                'description' => 'Świetny obraz na płótnie', 
                'price' => '125',
                'category' => 3,
                'state' => "nowy",
                'auction_type' => 'buy_now',
                'delivery_time' => '96',
                'duration' => '0',
                'quantity' => '100',
                'is_sold_out' => '0',
                'delivery_type' => [
                    3, 5, 6
                ]
            ],
            [
                'name' => 'Paczka Gwoździ x500', 
                'description' => 'Świetne gwoźdźie', 
                'price' => '40',
                'category' => 3,
                'state' => "nowy",
                'auction_type' => 'buy_now',
                'delivery_time' => '144',
                'duration' => '21',
                'quantity' => '35',
                'is_sold_out' => '0',
                'delivery_type' => [
                    1
                ]
            ],
            [
                'name' => 'Kabel usb', 
                'description' => 'Super nowy kabel usb', 
                'price' => '12',
                'category' => 1,
                'state' => "bardzo dobry",
                'auction_type' => 'auction',
                'delivery_time' => '48h',
                'duration' => '0',
                'quantity' => '0',
                'is_sold_out' => '1',
                'delivery_type' => [
                    2
                ]
            ],
            [
                'name' => 'Słuchawki', 
                'description' => 'Super nowe i świetne słuchawki', 
                'price' => '100',
                'category' => 1,
                'state' => "nowy",
                'auction_type' => 'free_advertisment',
                'delivery_time' => '48h',
                'duration' => '0',
                'quantity' => '0',
                'is_sold_out' => '0',
                'delivery_type' => []
            ]
        ];
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            CategoryFixtures::class,
            DeliveryTypeFixtures::class
        );
    }
}
