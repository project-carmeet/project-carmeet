<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Event;
use App\Factory\EventFactory;
use App\Form\Type\Event\EventType;
use App\Mapper\EventMapper;
use App\Model\Form\EventModel;
use App\Repository\EventRepository;
use App\Security\EventAction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

final class EditController extends AbstractController
{
    /**
     * @var EventRepository
     */
    protected $eventRepository;

    /**
     * @var EventFactory
     */
    protected $eventFactory;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventMapper
     */
    private $eventMapper;

    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $entityManager,
        EventFactory $eventFactory,
        EventMapper $eventMapper,
        EventRepository $eventRepository
    ) {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->eventFactory = $eventFactory;
        $this->eventMapper = $eventMapper;
        $this->eventRepository = $eventRepository;
    }

    public function __invoke(string $id): Response
    {
        $event = $this->getEvent($id);

        $this->denyAccessUnlessGranted(EventAction::EDIT, $event);

        $form = $this->createForm(EventType::class, $this->eventFactory->createEventModelFromEntity($event));
        $form->handleRequest($this->requestStack->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (!$data instanceof EventModel) {
                throw new UnexpectedValueException(sprintf('Expected form data to be instance of "%s".', EventModel::class));
            }

            $this->eventMapper->mapEventModelToEntity($data, $event);
            $this->entityManager->persist($event);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_event_view', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('event/edit.html.twig', [
            'form' => $form->createView(),
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
