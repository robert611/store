<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class ProductVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['PRODUCT_SHOW_POST_MESSAGE', 'CAN_SEE_PRODUCT'])
            && $subject instanceof \App\Entity\Product;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'PRODUCT_SHOW_POST_MESSAGE':
                return $this->canSeeMessageAfterPostingProduct($subject, $user);
                break;
            case 'CAN_SEE_PRODUCT': 
                return $this->canSeeProduct($subject, $user);
                break;
        }

        return false;
    }

    private function canSeeMessageAfterPostingProduct($product, $user)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        if ($user->getId() !== $product->getOwner()->getId())
        {
            return false;
        }

        return true;
    }

    public function canSeeProduct($product, $user)
    {
        if ($product->getIsDeleted()) {
            (new Session)->getFlashBag()->add('warning', 'Ten produkt został usunięty.');
            return false;
        }

        return true;
    }
}
