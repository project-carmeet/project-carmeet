<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Security\EventAction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class ReopenController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EventRepository
     */
    protected $eventRepository;

    public function __construct(EntityManagerInterface $entityManager, EventRepository $eventRepository)
    {
        $this->entityManager = $entityManager;
        $this->eventRepository = $eventRepository;
    }

    public function __invoke(string $id): Response
    {
        $event = $this->getEvent($id);

        $this->denyAccessUnlessGranted(EventAction::REOPEN, $event);

        $event->setCancellationDate(null);
        $this->entityManager->persist($event);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_event_view', [
            'id' => $event->getId(),
        ]);
    }

    private function getEvent(string $id): Event
    {
        $event = $this->eventRepository->find($id);
        if (!$event instanceof Event) {
            throw $this->createNotFoundException(sprintf('Could not find event by id "%s".', $id));
        }

        return $event;
    }
}
