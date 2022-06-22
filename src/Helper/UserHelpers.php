<?php

namespace App\Helper;

use App\Entity\User;

class UserHelpers
{
    public static function displayPseudo(User $user): string
    {
        $restrictedUser = !$user->isEnabled() || null !== $user->getBlockedAt();

        if ($restrictedUser) {
            return 'Utilisateur anonyme';
        }

        return $user->getPseudo();
    }
}
