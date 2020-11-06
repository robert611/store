<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\DeliveryType;

class DeliveryTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $deliveryTypes = $this->getDeliveryTypes();

        foreach ($deliveryTypes as $key => $data) {
            $deliveryType = new DeliveryType();
            $deliveryType->setName($data['name']);
            $deliveryType->setDefaultPrice($data['default_price']);
            $deliveryType->setPayment($data['payment']);

            $manager->persist($deliveryType);

            $this->addReference('deliveryType.' . $key, $deliveryType);
        }

        $manager->flush();
    }

    public function getDeliveryTypes()
    {
        return [
            ['name' => 'List polecony priorytetowy', 'default_price' => '9.4', 'payment' => 'prepayment'],
            ['name' => 'Dostawa przez sprzedającego - przedpłata', 'default_price' => '10', 'payment' => 'prepayment'],
            ['name' => 'Paczka pocztowa priorytetowa', 'default_price' => '14', 'payment' => 'prepayment'],
            ['name' => 'Odbiór w punkcie: Paczkomaty 24/7 InPost - przedpłata', 'default_price' => '11.99', 'payment' => 'prepayment'],
            ['name' => 'Przesyłka kurierska - przedłpata', 'default_price' => '11.5', 'payment' => 'prepayment'],
            ['name' => 'Przesyłka kurierska - pobranie', 'default_price' => '15.5', 'payment' => 'cash-on-delivery'],
        ];
    }
}
