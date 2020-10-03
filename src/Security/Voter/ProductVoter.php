<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['PRODUCT_SHOW_POST_MESSAGE'])
            && $subject instanceof \App\Entity\Product;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'PRODUCT_SHOW_POST_MESSAGE':
                return $this->canSeeMessageAfterPostingProduct($subject, $user);
                break;;
        }

        return false;
    }

    private function canSeeMessageAfterPostingProduct($product, $user)
    {
        if ($user->getId() !== $product->getOwner()->getId())
        {
            return false;
        }

        return true;
    }
}
