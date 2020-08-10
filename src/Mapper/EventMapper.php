<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Entity\Event;
use App\Model\Form\EventModel;
use DateTime;
use DateTimeImmutable;
use UnexpectedValueException;

final class EventMapper
{
    public function mapEventModelToEntity(EventModel $model, Event $entity): void
    {
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
    }
}
