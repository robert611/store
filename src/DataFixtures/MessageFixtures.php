<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Message;
use App\DataFixtures\ConversationFixtures;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $messages = $this->getMessages();

        foreach ($messages as $key => $data) {
            $message = new Message();

            $message->setAuthor($this->getReference("user." . ($data['author_id'] - 1)));
            $message->setText($data['text']);
            $message->setCreatedAt(new \DateTime());
            $message->setConversation($this->getReference("conversation." . ($data['conversation_id'] - 1)));

            $manager->persist($message);

            $this->addReference('message.'. $key, $message);
        }

        $manager->flush();
    }

    public function getMessages()
    {
        return [
            [
                'author_id' => 2,
                'text' => 'Hello, I am intrested in buying your product',
                'conversation_id' => 1
            ],
            [
                'author_id' => 1,
                'text' => 'I bet you are',
                'conversation_id' => 1
            ],
            [
                'author_id' => 3,
                'text' => 'Hello, I am intrested in buying your product',
                'conversation_id' => 2
            ],
            [
                'author_id' => 1,
                'text' => 'I bet you are',
                'conversation_id' => 2
            ],
            [
                'author_id' => 4,
                'text' => 'Hello, I am intrested in buying your product',
                'conversation_id' => 3
            ],
            [
                'author_id' => 1,
                'text' => 'I bet you are',
                'conversation_id' => 3
            ],
        ];
    }

    public function getDependencies()
    {
        return array(
            ConversationFixtures::class
        );
    }
}
