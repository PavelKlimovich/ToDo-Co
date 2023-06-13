<?php

namespace App\Services;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class UserService
{
    private Security $security; 

    public function __construct( Security $security)
    {
        $this->security = $security;
    }

    public function ifAuthorisation(): bool
    {
        /** @var User $user */
        $user = $this->security->getUser();
        if ($user?->getRoles()[0] == "ROLE_ADMIN") {
            return true;
        }

        return false;
    }

    public function taskAuthorisation(Task $task): bool
    {
        /** @var User $user */
        $user = $this->security->getUser();
        if (($user?->getRoles()[0] == "ROLE_ADMIN" && $task->getUser() == null) || $task->getUser()?->getId() == $user?->getId()) {
            return true;
        }

        return false;
    }
}