<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Event;
use App\Event\Event\ReopenEvent;
use App\Repository\EventRepository;
use App\Security\EventAction;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class ReopenController extends AbstractController
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var EventRepository
     */
    protected $eventRepository;

    public function __construct(EventDispatcherInterface $eventDispatcher, EventRepository $eventRepository)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->eventRepository = $eventRepository;
    }

    public function __invoke(string $id): Response
    {
        $event = $this->eventRepository->find($id);
        if (!$event instanceof Event) {
            throw $this->createNotFoundException(sprintf('Could not find event by id "%s".', $id));
        }

        $this->denyAccessUnlessGranted(EventAction::REOPEN, $event);
        $this->eventDispatcher->dispatch(new ReopenEvent($event));

        return $this->redirectToRoute('app_event_view', [
            'id' => $event->getId(),
        ]);
    }
}
