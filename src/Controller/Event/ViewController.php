<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Security\EventAction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class ViewController extends AbstractController
{
    /**
     * @var EventRepository
     */
    protected $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function __invoke(string $id): Response
    {
        $event = $this->eventRepository->find($id);
        if (!$event instanceof Event) {
            throw $this->createNotFoundException(sprintf('Could not find event by id "%s".', $id));
        }

        $this->denyAccessUnlessGranted(EventAction::VIEW, $event);

        return $this->render('event/view.html.twig', [
            'event' => $event,
        ]);
    }
}
