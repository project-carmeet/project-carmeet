<?php

declare(strict_types=1);

namespace App\Subscriber\Event;

use App\Event\Event\CancelEvent;
use App\Event\Event\ReopenEvent;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CancelSubscriber implements EventSubscriberInterface
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
            CancelEvent::class => ['cancel'],
            ReopenEvent::class => ['reopen'],
        ];
    }

    public function cancel(CancelEvent $cancelEvent): void
    {
        $event = $cancelEvent->getEvent();
        $event->setCancellationDate(new DateTimeImmutable());

        $this->entityManager->persist($event);
        $this->entityManager->flush();
    }

    public function reopen(ReopenEvent $reopenEvent): void
    {
        $event = $reopenEvent->getEvent();
        $event->setCancellationDate(null);

        $this->entityManager->persist($event);
        $this->entityManager->flush();
    }
}
