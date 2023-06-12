<?php

namespace App\Services;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class UserService
{
    private Security $security; 

    public function __construct( Security $security)
    {
        $this->security = $security;
    }

    public function ifAuthorisation(User $user): bool
    {
        /** @var User $user */
        $user = $this->security->getUser();
        dd($user);
        if ($user?->getRoles() == '["ROLE_ADMIN"]') {
            return true;
        }

        return false;
    }
}