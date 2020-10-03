<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class ConversationVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['CONVERSATION_VIEW'])
            && $subject instanceof \App\Entity\Conversation;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'CONVERSATION_VIEW':
                return $this->canSee($subject, $user);
                break;
        }
        
        return false;
    }

    private function canSee($conversation, $user)
    {
        if ($conversation->getAuthor()->getId() !== $user->getId() && $conversation->getRecipient()->getId() !== $user->getId()) {
            (new Session)->getFlashBag()->add('warning', 'Nie możesz zobaczyć cudzej konwersacji.');
            
            return false;
        }

        return true;
    }
}
