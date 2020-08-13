<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Event\User\RegisterEvent;
use App\Form\Type\User\RegistrationType;
use App\Model\Form\UserModel;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

final class RegisterController extends AbstractController
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(RequestStack $requestStack, EventDispatcherInterface $eventDispatcher)
    {
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(): Response
    {
        $form = $this->createForm(RegistrationType::class);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserModel|mixed $data */
            $data = $form->getData();
            if (!$data instanceof UserModel) {
                throw new UnexpectedValueException('Invalid object received as form data.');
            }

            $event = new RegisterEvent($data);
            $this->eventDispatcher->dispatch($event);

            $this->addFlash('success', 'New user has been registered with uuid ' . $event->getUser()->getId() . '.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
