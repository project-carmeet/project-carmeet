<?php

declare(strict_types=1);

namespace App\Event\Event;

use App\Entity\Event as EventEntity;
use App\Model\Form\EventModel;
use Symfony\Contracts\EventDispatcher\Event;

final class EditEvent extends Event
{
    /**
     * @var EventEntity
     */
    protected $event;

    /**
     * @var EventModel
     */
    private $eventModel;

    public function __construct(EventEntity $event, EventModel $eventModel)
    {
        $this->event = $event;
        $this->eventModel = $eventModel;
    }

    public function getEvent(): EventEntity
    {
        return $this->event;
    }

    public function getEventModel(): EventModel
    {
        return $this->eventModel;
    }
}
