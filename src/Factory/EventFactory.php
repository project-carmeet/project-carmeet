<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Event;
use App\Model\Form\EventModel;
use DateTime;
use DateTimeImmutable;
use UnexpectedValueException;

final class EventFactory
{
    public function createEntityFromEventModel(EventModel $eventModel): Event
    {
        $name = $eventModel->getName();
        if (null === $name) {
            throw new UnexpectedValueException('Expected name to be set.');
        }

        $dateFrom = $eventModel->getDateFrom();
        if (null === $dateFrom) {
            throw new UnexpectedValueException('Expected date from to be set.');
        }

        $dateUntil = $eventModel->getDateUntil();
        if (null === $dateUntil) {
            throw new UnexpectedValueException('Expected date until to be set.');
        }

        return new Event(
            $name,
            $eventModel->getDescription(),
            new DateTimeImmutable('@' . $dateFrom->getTimestamp()),
            new DateTimeImmutable('@' . $dateUntil->getTimestamp()),
            $eventModel->getUser()
        );
    }

    public function createEventModelFromEntity(Event $event): EventModel
    {
        $model = new EventModel($event->getUser());
        $model->setName($event->getName());
        $model->setDescription($event->getDescription());
        $model->setDateFrom(new DateTime('@' . $event->getDateFrom()->getTimestamp()));
        $model->setDateUntil(new DateTime('@' . $event->getDateUntil()->getTimestamp()));

        return $model;
    }
}
