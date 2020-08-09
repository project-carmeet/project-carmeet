<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Event;
use App\Entity\User;
use App\Security\EventAction;
use App\Security\Role;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class EventVoter extends Voter
{
    /**
     * @param Event|mixed $subject
     */
    protected function supports(string $attribute, $subject): bool
    {
        return ($subject instanceof Event || null === $subject) && EventAction::isAction($attribute);
    }

    /**
     * @param Event|null $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if ($attribute === EventAction::VIEW) {
            return true;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        if (null === $subject && $attribute === EventAction::CREATE) {
            return true;
        }

        if (null !== $subject && $attribute === EventAction::EDIT) {
            if ($user->hasRole(Role::ADMIN)) {
                return true;
            }

            return $subject->getUser()->getEmail() === $user->getEmail();
        }

        if (null !== $subject && $attribute === EventAction::CANCEL) {
            return ($subject->getUser()->getEmail() === $user->getEmail() || $user->hasRole(Role::ADMIN)) && !$subject->isCancelled();
        }

        if (null !== $subject && $attribute === EventAction::REOPEN) {
            return $user->hasRole(Role::ADMIN) && $subject->isCancelled();
        }

        throw new LogicException('Attribute not supported.');
    }
}
