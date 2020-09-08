<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\DeliveryType;
use App\DataFixtures\ProductFixtures;

class DeliveryTypeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $deliveryTypes = $this->getDeliveryTypes();

        foreach ($deliveryTypes as $key => $data) {
            $deliveryType = new DeliveryType();
            $deliveryType->setName($data['name']);
            $deliveryType->setDefaultPrice($data['default_price']);
            $deliveryType->addProduct($this->getReference("product"));

            $manager->persist($deliveryType);

            $this->addReference('deliveryType.' . $key, $deliveryType);
        }

        $manager->flush();
    }

    public function getDeliveryTypes()
    {
        return [
            ['name' => 'List polecony priorytetowy', 'default_price' => '9.4'],
            ['name' => 'Dostawa przez sprzedającego - przedpłata', 'default_price' => '10'],
            ['name' => 'Paczka pocztowa priorytetowa', 'default_price' => '14'],
            ['name' => 'Odbiór w punkcie: Paczkomaty 24/7 InPost - przedpłata', 'default_price' => '11.99'],
            ['name' => 'Przesyłka kurierska - przedłpata', 'default_price' => '11.5'],
            ['name' => 'Przesyłka kurierska - pobranie', 'default_price' => '15.5'],
        ];
    }

    public function getDependencies()
    {
        return array(
            ProductFixtures::class,
        );
    }
}
