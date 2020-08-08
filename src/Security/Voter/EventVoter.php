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

        if ($user->hasRole(Role::ADMIN)) {
            return true;
        }

        if (null === $subject && $attribute === EventAction::CREATE) {
            return true;
        }

        if (null !== $subject && ($attribute === EventAction::EDIT || $attribute === EventAction::DELETE)) {
            return $subject->getUser()->getEmail() === $user->getEmail();
        }

        throw new LogicException('Attribute not supported.');
    }
}
