<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class PurchaseVoter extends Voter
{
    protected function supports($attribute, $product)
    {
        return in_array($attribute, ['PURCHASE_BUY', 'PURCHASE_BUY_WITH_BASKET'])
            && ($product instanceof \App\Entity\Product or $product instanceof \Doctrine\Common\Collections\ArrayCollection);
    }

    protected function voteOnAttribute($attribute, $product, TokenInterface $token)
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
  
        switch ($attribute) {
            case 'PURCHASE_BUY': 
                return $this->canBuy($product, $user);
                break;
            case 'PURCHASE_BUY_WITH_BASKET': 
                return $this->canBuyWithBasket($product, $user); /* In this case $product is an array of products */
        }

        return false;
    }

    private function canBuy($product, $user)
    {
        if ($user->getId() === $product->getOwner()->getId()) {
            (new Session)->getFlashBag()->add('warning', 'Nie możesz kupić własnego przedmiotu.');

            return false;
        }

        return true;
    }

    public function canBuyWithBasket($products, $user)
    {
        $products->map(function($product) use ($user) {
            if ($user->getId() === $product->getOwner()->getId()) {
                (new Session)->getFlashBag()->add('warning', 'Nie możesz kupić własnego przedmiotu.');
    
                return false;
            }
        });

        if (count($products) == 0) return false;

        return true;
    }
}
