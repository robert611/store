<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class AuctionVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['AUCTION_BID'])
            && $subject instanceof \App\Entity\Product;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'AUCTION_BID':
                return $this->canBid($subject, $user);
                break;
        }
        
        return false;
    }

    private function canBid($product, $user)
    {
        if ($product->getOwner()->getId() == $user->getId()) {
            (new Session)->getFlashBag()->add('warning', 'Nie możesz zalicytować własnego przedmiotu.');
            return false;
        }

        if ($product->getAuctionType() !== "auction") {
            (new Session)->getFlashBag()->add('warning', 'Ten przedmiot nie podlega licytacji.');
            return false;
        }
       
        return true;
    }
}