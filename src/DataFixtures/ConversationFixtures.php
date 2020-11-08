<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Conversation;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\DataFixtures\ProductFixtures;

class ConversationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $conversations = $this->getConversations();

        foreach ($conversations as $key => $data) {
            $conversation = new Conversation();

            $conversation->setAuthor($this->getReference("user." . ($data['author_id'] - 1)));
            $conversation->setRecipient($this->getReference("user." . ($data['recipient_id'] - 1)));
            $conversation->setProduct($this->getReference("product." . ($data['product_id'] - 1)));

            $manager->persist($conversation);

            $this->addReference('conversation.'. $key, $conversation);
        }

        $manager->flush();
    }

    public function getConversations()
    {
        return [
            [
                'author_id' => 2,
                'recipient_id' => 1,
                'product_id' => 7
            ],
            [
                'author_id' => 3,
                'recipient_id' => 1,
                'product_id' => 7
            ],
            [
                'author_id' => 4,
                'recipient_id' => 1,
                'product_id' => 7
            ]
        ];
    }

    public function getDependencies()
    {
        return array(
            ProductFixtures::class
        );
    }
}
