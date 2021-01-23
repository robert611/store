<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class ProductOpinionVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['CAN_ADD_PRODUCT_OPINION', 'CAN_EDIT_PRODUCT_OPINION'])
            && $subject instanceof \App\Entity\Product;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'CAN_ADD_PRODUCT_OPINION':
                return $this->canAddProductOpinion($subject, $user);
                break;
            case 'CAN_EDIT_PRODUCT_OPINION':
                return $this->canEditProductOpinion($subject, $user);
                break;
        }

        return false;
    }

    private function canAddProductOpinion($product, $user)
    {
        $isProductBought = false;

        foreach ($user->getPurchases() as $purchase)
        {
            foreach ($purchase->getPurchaseProducts() as $purchaseProduct)
            {
                if ($product->getId() == $purchaseProduct->getProduct()->getId())
                {
                    $isProductBought = true;
                }
            }
        }

        if (!$isProductBought) {
            (new Session)->getFlashBag()->add('warning', 'Możesz dodawać opinię tylko tym produktom, które kupiłeś/aś.');
            return false;
        }

        if ($user->hasOpinionOnProduct($product)) {
            (new Session)->getFlashBag()->add('warning', 'Już dodałeś opinię tego produktu.');
            return false;
        }

        return true;
    }

    private function canEditProductOpinion($product, $user)
    {
        if ($user->hasOpinionOnProduct($product) == null) {
            (new Session)->getFlashBag()->add('warning', 'Najpierw musisz dodać opinię tego produktu.');
            return false;
        }

        return true;
    }
}