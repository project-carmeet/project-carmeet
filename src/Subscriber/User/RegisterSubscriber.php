<?php

declare(strict_types=1);

namespace App\Subscriber\User;

use App\Entity\User;
use App\Event\User\RegisterEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use UnexpectedValueException;

final class RegisterSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RegisterEvent::class => ['register'],
        ];
    }

    public function register(RegisterEvent $registerEvent): void
    {
        $userModel = $registerEvent->getUserModel();
        $username = $userModel->getUsername();
        if (null === $username) {
            throw new UnexpectedValueException('Expected username to be set but null was found.');
        }

        $plainPassword = $userModel->getPassword();
        if (null === $plainPassword) {
            throw new UnexpectedValueException('Expected password to be set but null was found.');
        }

        $email = $userModel->getEmail();
        if (null === $email) {
            throw new UnexpectedValueException('Expected email to be set but null was found.');
        }

        $user = new User($username, $email);
        $password = $this->userPasswordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $registerEvent->setUser($user);
    }
}
