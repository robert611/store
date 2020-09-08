<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categories = $this->getCategories();

        foreach ($categories as $key => $data) {
            $category = new Category();
            $category->setName($data['name']);
            $category->setIcon($data['icon']);

            $manager->persist($category);

            $manager->flush();

            $this->addReference('category.' . $key, $category);
        }
    }

    public function getCategories()
    {
        return [
            ['name' => 'Elektronika', 'icon' => 'computer'],
            ['name' => 'Moda', 'icon' => 'style'],
            ['name' => 'Dom i ogród', 'icon' => 'home'],
            ['name' => 'Supermarket', 'icon' => 'shopping_basket'],
            ['name' => 'Dziecko', 'icon' => 'child_friendly'],
            ['name' => 'Uroda', 'icon' => 'face'],
            ['name' => 'Zdrowie', 'icon' => 'healing'],
            ['name' => 'Kultura i rozrywka', 'icon' => 'panorama'],
            ['name' => 'Sport i turystyka', 'icon' => 'directions_run'],
            ['name' => 'Motoryzacja', 'icon' => 'directions_car'],
            ['name' => 'Nieruchomości', 'icon' => 'home'],
        ];
    }
}
