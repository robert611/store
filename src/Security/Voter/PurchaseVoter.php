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
            && $product instanceof \App\Entity\Product;
    }

    protected function voteOnAttribute($attribute, $product, TokenInterface $token)
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
  
        switch ($attribute) {
            case 'PURCHASE_NEW':
                return $this->canPurchase($product, $user);
                break;
        }

        return false;
    }

    private function canPurchase($product, $user)
    {
        if ($user->getId() === $product->getOwner()->getId()) {
            (new Session)->getFlashBag()->add('warning', 'Nie możesz kupić własnego przedmiotu.');

            return false;
        }

        return true;
    }
}
