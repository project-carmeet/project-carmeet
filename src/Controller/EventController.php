<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Factory\EventFactory;
use App\Form\Type\Event\EventType;
use App\Mapper\EventMapper;
use App\Model\Form\EventModel;
use App\Repository\EventRepository;
use App\Security\EventAction;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use UnexpectedValueException;

final class EventController extends AbstractController
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EventRepository
     */
    protected $eventRepository;

    /**
     * @var EventFactory
     */
    protected $eventFactory;

    /**
     * @var EventMapper
     */
    protected $eventMapper;

    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $entityManager,
        EventRepository $eventRepository,
        EventFactory $eventFactory,
        EventMapper $eventMapper
    ) {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->eventRepository = $eventRepository;
        $this->eventFactory = $eventFactory;
        $this->eventMapper = $eventMapper;
    }

    /**
     * @Route("/event/view/{id}", name="app_event_view")
     */
    public function view(string $id): Response
    {
        $event = $this->getEvent($id);

        $this->denyAccessUnlessGranted(EventAction::VIEW, $event);

        return $this->render('event/view.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * @Route("/event/create", name="app_event_create")
     */
    public function create(): Response
    {
        $this->denyAccessUnlessGranted(EventAction::CREATE);

        $form = $this->createForm(EventType::class, new EventModel($this->getUserEntity()));
        $form->handleRequest($this->requestStack->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var mixed $data */
            $data = $form->getData();
            if (!$data instanceof EventModel) {
                throw new UnexpectedValueException(sprintf('Expected form data to be instance of "%s".', EventModel::class));
            }

            $event = $this->eventFactory->createEntityFromEventModel($data);
            $this->entityManager->persist($event);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_event_view', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('event/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/event/edit/{id}", name="app_event_edit")
     */
    public function edit(string $id): Response
    {
        $event = $this->getEvent($id);

        $this->denyAccessUnlessGranted(EventAction::EDIT, $event);

        $form = $this->createForm(EventType::class, $this->eventFactory->createEventModelFromEntity($event));
        $form->handleRequest($this->requestStack->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EventModel|mixed $data */
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

    /**
     * @Route("/event/cancel/{id}", name="app_event_cancel")
     */
    public function cancel(string $id): Response
    {
        $event = $this->getEvent($id);

        $this->denyAccessUnlessGranted(EventAction::CANCEL, $event);

        $event->setCancellationDate(new DateTimeImmutable());
        $this->entityManager->persist($event);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_event_view', [
            'id' => $event->getId(),
        ]);
    }

    /**
     * @Route("/event/reopen/{id}", name="app_event_reopen")
     */
    public function reopen(string $id): Response
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

    private function getUserEntity(): User
    {
        $user = $this->getUser();
        if (null === $user) {
            throw new LogicException('User not found, user is probably not logged in.');
        }

        if (!$user instanceof User) {
            throw new UnexpectedValueException(sprintf('Expeted logged in user to be instance of "%s".', User::class));
        }

        return $user;
    }
}
