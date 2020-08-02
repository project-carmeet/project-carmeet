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
    public function createFromEventModel(EventModel $eventModel): Event
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

        if ($dateFrom instanceof DateTime) {
            $dateFrom = DateTimeImmutable::createFromMutable($dateFrom);
        }

        if ($dateUntil instanceof DateTime) {
            $dateUntil = DateTimeImmutable::createFromMutable($dateUntil);
        }

        return new Event(
            $name,
            $eventModel->getDescription(),
            $dateFrom,
            $dateUntil,
            $eventModel->getUser()
        );
    }
}
