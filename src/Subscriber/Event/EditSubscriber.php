<?php

declare(strict_types=1);

namespace App\Subscriber\Event;

use App\Event\Event\EditEvent;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use UnexpectedValueException;

final class EditSubscriber implements EventSubscriberInterface
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
            EditEvent::class => ['edit'],
        ];
    }

    public function edit(EditEvent $event): void
    {
        $model = $event->getEventModel();
        $entity = $event->getEvent();

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

        if ($dateFrom instanceof DateTime) {
            $dateFrom = DateTimeImmutable::createFromMutable($dateFrom);
        }

        if ($dateUntil instanceof DateTime) {
            $dateUntil = DateTimeImmutable::createFromMutable($dateUntil);
        }

        $entity->setName($name);
        $entity->setDescription($model->getDescription());
        $entity->setDateFrom($dateFrom);
        $entity->setDateUntil($dateUntil);
        $entity->setUser($model->getUser());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
