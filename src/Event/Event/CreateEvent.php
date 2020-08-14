<?php

declare(strict_types=1);

namespace App\Event\Event;

use App\Entity\Event as EventEntity;
use App\Model\Form\EventModel;
use LogicException;
use Symfony\Contracts\EventDispatcher\Event;

final class CreateEvent extends Event
{
    /**
     * @var EventModel
     */
    private $eventModel;

    /**
     * @var EventEntity|null
     */
    private $event;

    public function __construct(EventModel $eventModel)
    {
        $this->eventModel = $eventModel;
    }

    public function getEventModel(): EventModel
    {
        return $this->eventModel;
    }

    public function getEvent(): EventEntity
    {
        if (null === $this->event) {
            throw new LogicException('No event set.');
        }

        return $this->event;
    }

    public function setEvent(EventEntity $event): void
    {
        $this->event = $event;
    }
}
