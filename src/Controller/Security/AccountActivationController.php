<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Event\User\ActivateEvent;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class AccountActivationController extends AbstractController
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(
        UserRepository $userRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userRepository = $userRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(string $token)
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
