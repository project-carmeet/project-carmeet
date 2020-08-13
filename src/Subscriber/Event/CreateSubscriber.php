<?php

declare(strict_types=1);

namespace App\Subscriber\Event;

use App\Entity\Event;
use App\Event\Event\CreateEvent;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use UnexpectedValueException;

final class CreateSubscriber implements EventSubscriberInterface
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
            CreateEvent::class => ['create'],
        ];
    }

    public function create(CreateEvent $event): void
    {
        $model = $event->getEventModel();

        $name = $model->getName();
        if (null === $name) {
            throw new UnexpectedValueException('Expected name to be set.');
        }

        $dateFrom = $model->getDateFrom();
        if (null === $dateFrom) {
            throw new UnexpectedValueException('Expected date from to be set.');
        }

        $dateUntil = $model->getDateUntil();
        if (null === $dateUntil) {
            throw new UnexpectedValueException('Expected date until to be set.');
        }

        $eventEntity = new Event(
            $name,
            $model->getDescription(),
            new DateTimeImmutable('@' . $dateFrom->getTimestamp()),
            new DateTimeImmutable('@' . $dateUntil->getTimestamp()),
            $model->getUser()
        );

        $this->entityManager->persist($eventEntity);
        $this->entityManager->flush();

        $event->setEvent($eventEntity);
    }
}
