<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\AuctionBid;

class AuctionBidFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $auctionBids = $this->getAuctionBids();

        foreach ($auctionBids as $key => $data) {
            $auctionBid = new AuctionBid();

            $auctionBid->setProduct($this->getReference("product." . ($data['product_id'] - 1)));
            $auctionBid->setUser($this->getReference("user." . ($data['user_id'] - 1)));
            $auctionBid->setBid($data['bid']);

            $manager->persist($auctionBid);

            $this->addReference('auctionBid.'. $key, $auctionBid);
        }

        $manager->flush();
    }

    public function getAuctionBids()
    {
        return [
            [
                'product_id' => 9,
                'user_id' => 2,
                'bid' => 6
            ],
            [
                'product_id' => 9,
                'user_id' => 3,
                'bid' => 7
            ]
        ];
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            ProductFixtures::class,
        );
    }
}
