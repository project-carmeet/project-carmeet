<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\Event;
use App\Event\Event\EditEvent;
use App\Form\Type\Event\EventType;
use App\Model\Form\EventModel;
use App\Repository\EventRepository;
use App\Security\EventAction;
use Psr\EventDispatcher\EventDispatcherInterface;
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
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        EventRepository $eventRepository
    ) {
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
        $this->eventRepository = $eventRepository;
    }

    public function __invoke(string $id): Response
    {
        $event = $this->eventRepository->find($id);
        if (!$event instanceof Event) {
            throw $this->createNotFoundException(sprintf('Could not find event by id "%s".', $id));
        }

        $this->denyAccessUnlessGranted(EventAction::EDIT, $event);

        $form = $this->createForm(EventType::class, EventModel::createFromEntity($event));
        $form->handleRequest($this->requestStack->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EventModel|mixed $data */
            $data = $form->getData();
            if (!$data instanceof EventModel) {
                throw new UnexpectedValueException(sprintf('Expected form data to be instance of "%s".', EventModel::class));
            }

            $this->eventDispatcher->dispatch(new EditEvent($event, $data));

            return $this->redirectToRoute('app_event_view', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('event/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
