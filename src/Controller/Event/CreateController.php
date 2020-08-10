<?php

declare(strict_types=1);

namespace App\Controller\Event;

use App\Entity\User;
use App\Factory\EventFactory;
use App\Form\Type\Event\EventType;
use App\Model\Form\EventModel;
use App\Security\EventAction;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

final class CreateController extends AbstractController
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EventFactory
     */
    private $eventFactory;

    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $entityManager,
        EventFactory $eventFactory
    ) {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->eventFactory = $eventFactory;
    }

    public function __invoke(): Response
    {
        $this->denyAccessUnlessGranted(EventAction::CREATE);

        $form = $this->createForm(EventType::class, new EventModel($this->getUserEntity()));
        $form->handleRequest($this->requestStack->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EventModel|mixed $data */
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
