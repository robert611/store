<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\ProductOpinion;
use App\DataFixtures\ProductFixtures;
use App\DataFixtures\UserFixtures;

class ProductOpinionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $productOpinions = $this->getProductOpinions();

        foreach ($productOpinions as $key => $opinionData) {
            $opinion = new ProductOpinion();

            $opinion->setMark($opinionData['mark']);
            $opinion->setOpinion($opinionData['opinion']);
            $opinion->setAdvantages($opinionData['advantages']);
            $opinion->setFlaws($opinionData['flaws']);
            $opinion->setProduct($this->getReference("product." . ($opinionData['product_id'] - 1)));
            $opinion->setUser($this->getReference("user." . ($opinionData['user_id'] - 1)));
            $opinion->setCreatedAt(new \DateTime());

            $manager->persist($opinion);

            $this->addReference('product_opinion.'. $key, $opinion);
        }

        $manager->flush();
    }

    public function getProductOpinions()
    {
        return [
            [
                'mark' => 5,
                'opinion' => 'Really nice, fast delivery',
                'advantages' => 'Versitale',
                'flaws' => '',
                'user_id' => 7,
                'product_id' => 8
            ],
            [
                'mark' => 3,
                'opinion' => 'Comfartable to use, but could be better',
                'advantages' => 'Easy to use',
                'flaws' => 'not that useful',
                'user_id' => 5,
                'product_id' => 8
            ]
        ];
    }

    public function getDependencies()
    {
        return array(
            ProductFixtures::class,
            UserFixtures::class
        );
    }
}
