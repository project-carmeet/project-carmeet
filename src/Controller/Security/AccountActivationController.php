<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Event\User\ActivateEvent;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class AccountActivationController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        UserRepository $userRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(string $token): Response
    {
        $user = $this->userRepository->findOneOrNullByActivationToken($token);
        if (null === $user) {
            throw $this->createNotFoundException(sprintf('No user found by activation token "%s".', $token));
        }

        $this->eventDispatcher->dispatch(new ActivateEvent($user));
        $this->addFlash('success', 'Account is activated.');

        return $this->redirectToRoute('app_login');
    }
}
