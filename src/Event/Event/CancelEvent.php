<?php

declare(strict_types=1);

namespace App\Event\Event;

use App\Entity\Event as EventEntity;
use Symfony\Contracts\EventDispatcher\Event;

final class CancelEvent extends Event
{
    /**
     * @var EventEntity
     */
    private $event;

    public function __construct(EventEntity $event)
    {
        $this->event = $event;
    }

    public function getEvent(): EventEntity
    {
        return $this->event;
    }
}
