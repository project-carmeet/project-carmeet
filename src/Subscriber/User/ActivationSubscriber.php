<?php

declare(strict_types=1);

namespace App\Subscriber\User;

use App\Event\User\ActivateEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ActivationSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ActivateEvent::class => ['activate'],
        ];
    }

    public function activate(ActivateEvent $event): void
    {
        $user = $event->getUser();
        $user->setActive(true);
        $user->setActivationToken(null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
