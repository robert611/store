<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $users = $this->getUsers();

        foreach ($users as $key => $data) {
            $user = new User();

            $user->setUsername($data['username']);
            $user->setRoles($data['roles']);
            $user->setPassword($this->encoder->encodePassword($user, $data['password']));
            $user->setEmail($data['email']);

            $manager->persist($user);

            $this->addReference('user.' . $key, $user);
        }

        $manager->flush();

    }

    public function getUsers()
    {
        return [
            ['username' => 'administrator', 'roles' => ['ROLE_USER', 'ROLE_ADMIN'], 'password' => 'password', 'email' => 'admin@interia.pl'],
            ['username' => 'Tomy', 'roles' => ['ROLE_USER'], 'password' => 'password', 'email' => 'casual_user@interia.pl'],
            ['username' => 'mario11', 'roles' => ['ROLE_USER'], 'password' => 'password', 'email' => 'mario11@interia.pl'],
            ['username' => 'Vhegar', 'roles' => ['ROLE_USER'], 'password' => 'password', 'email' => 'vhegar@interia.pl'],
            ['username' => 'no_conversations', 'roles' => ['ROLE_USER'], 'password' => 'password', 'email' => 'noconversations@interia.pl'],
            ['username' => 'no_basket_products', 'roles' => ['ROLE_USER'], 'password' => 'password', 'email' => 'nobasketproducts@interia.pl'],
            ['username' => 'opinion_user', 'roles' => ['ROLE_USER'], 'password' => 'password', 'email' => 'opinion_user@interia.pl'],
            ['username' => 'Robb12', 'roles' => ['ROLE_USER'], 'password' => 'password', 'email' => 'user_without_opinions@interia.pl']
        ];
    }
}
