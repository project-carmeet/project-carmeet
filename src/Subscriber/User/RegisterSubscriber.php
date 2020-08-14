<?php

declare(strict_types=1);

namespace App\Subscriber\User;

use App\Entity\User;
use App\Event\User\RegisterEvent;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $userPasswordEncoder,
        UrlGeneratorInterface $urlGenerator,
        Swift_Mailer $mailer
    ) {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RegisterEvent::class => [
                ['register', 2],
                ['sendActivationEmail', 1],
            ],
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

    public function sendActivationEmail(RegisterEvent $event): void
    {
        $user = $event->getUser();
        $user->setActivationToken(Uuid::uuid4()->toString());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $messsage = new Swift_Message('Forgot password', sprintf(
            'To activate this account, use <a href="%s">this link.</a>',
            $this->urlGenerator->generate('app_security_account_activation', [
                'token' => $user->getActivationToken(),
            ], UrlGeneratorInterface::ABSOLUTE_URL)
        ));

        $messsage->setContentType('text/html');

        $messsage->setFrom('hello_world@carmeet.internal');
        $messsage->setTo($user->getEmail());

        $this->mailer->send($messsage);
    }
}
