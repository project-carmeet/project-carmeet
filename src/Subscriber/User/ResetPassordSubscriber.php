<?php

declare(strict_types=1);

namespace App\Subscriber\User;

use App\Event\User\ResetPasswordEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class ResetPassordSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager)
    {
        $this->encoder = $encoder;
        $this->entityManager = $entityManager;
    }

    /**
     * @return array<mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ResetPasswordEvent::class => 'resetPassword',
        ];
    }

    public function resetPassword(ResetPasswordEvent $event): void
    {
        $user = $event->getUser();

        $encodedPassword = $this->encoder->encodePassword($user, $event->getNewPassword());
        $user->setPassword($encodedPassword);
        $user->clearResetPasswordToken();

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
