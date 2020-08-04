<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Factory\EventFactory;
use App\Form\Type\Event\EventType;
use App\Model\Form\EventModel;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use UnexpectedValueException;

final class EventController extends AbstractController
{
    /**
     * @Route("/event/view/{id}", name="app_event_view")
     */
    public function view(string $id, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->find($id);
        if (null === $event) {
            throw $this->createNotFoundException(sprintf('Could not find event by id "%s".', $id));
        }

        return $this->render('event/view.html.twig', [
            'event' => $eventRepository->find($id),
        ]);
    }

    /**
     * @Route("/event/create", name="app_event_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager, EventFactory $eventFactory): Response
    {
        $form = $this->createForm(EventType::class, new EventModel($this->getUserEntity()));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (!$data instanceof EventModel) {
                throw new UnexpectedValueException(sprintf('Expected form data to be instance of "%s".', EventModel::class));
            }

            $event = $eventFactory->createFromEventModel($data);
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_view', [
                'id' => $event->getId(),
            ]);
        }

        return $this->render('event/create.html.twig', [
            'form' => $form->createView(),
        ]);
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
