<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\UserAddress;
use App\DataFixtures\UserFixtures;

class UserAddressFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $addresses = $this->getAddresses();

        foreach ($addresses as $key => $address) {
            $userAddress = new UserAddress();

            $userAddress->setName($address['name']);
            $userAddress->setSurname($address['surname']);
            $userAddress->setAddress($address['address']);
            $userAddress->setZipCode($address['zip_code']);
            $userAddress->setCity($address['city']);
            $userAddress->setCountry($address['country']);
            $userAddress->setPhoneNumber($address['phone_number']);
            $userAddress->setUser($this->getReference("user." . ($address['user_id'] - 1)));
            
            $manager->persist($userAddress);

            $this->addReference('userAddress.'. $key, $userAddress);
        }

        
        $manager->flush();
    }

    private function getAddresses()
    {
        return [
            [
                'user_id' => 1,
                'name' => 'Johny',
                'surname' => 'Admin',
                'address' => 'Berns 12',
                'zip_code' => '78-214',
                'city' => 'New York',
                'country' => 'Usa',
                'phone_number' => '733-128-588'
            ], 
            [
                'user_id' => 2,
                'name' => 'Tomy',
                'surname' => 'Casual',
                'address' => 'Defenders 96',
                'zip_code' => '24-198',
                'city' => 'New York',
                'country' => 'Usa',
                'phone_number' => '535-148-988'
            ]
        ];
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class
        );
    }
}
