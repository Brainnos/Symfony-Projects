<?php

namespace App\Security;

use App\Entity\Users ;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof Users) {
            return;
        }

        // // user is deleted, show a generic Account Not Found message.
        // if ($user->isDeleted()) {
        //     throw new AccountDeletedException();
        // }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof Users) {
            return;
        }

        // user account is expired, the user may be notified
        if ($user->getActive() != 1) {
            throw new CustomUserMessageAuthenticationException("Vous n'avez pas encore valider votre email, veuillez vérifier vos emails");
        }
    }
}