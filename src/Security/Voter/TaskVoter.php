<?php

namespace App\Security\Voter;

use App\Entity\Task;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    public const EDIT = 'TASK_EDIT';
    public const TOGGLE = 'TASK_TOGGLE';
    public const DELETE = 'TASK_DELETE';


    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::TOGGLE, self::DELETE]) && $subject instanceof \App\Entity\Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject, $user);
            case self::TOGGLE:
                return $this->canToggle($subject, $user);
            case self::DELETE:
                return $this->canDelete($subject, $user);
        }

        return false;
    }

    public function canEdit(Task $task, UserInterface $user): bool
    {
        return $task->getUser() === $user;
    }

    public function canToggle(Task $task, UserInterface $user): bool
    {
        return $task->getUser() === $user;
    }

    public function canDelete(Task $task, UserInterface $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles()) && is_null($task->getUser()) || $task->getUser() === $user;
    }
}
