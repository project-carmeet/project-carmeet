<?php

declare(strict_types=1);

namespace App\Subscriber\User;

use App\Event\User\ForgotPasswordEvent;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ForgotPasswordSubscriber implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, Swift_Mailer $mailer)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->mailer = $mailer;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ForgotPasswordEvent::class => [
                ['generateToken', 2],
                ['emailLinkToUser', 1],
            ],
        ];
    }

    public function generateToken(ForgotPasswordEvent $forgotPasswordEvent): void
    {
        $token = Uuid::uuid4();
        $user = $forgotPasswordEvent->getUser();
        $user->setResetPasswordToken($token->toString());
        $user->setResetPasswordTimestamp(new DateTimeImmutable());

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function emailLinkToUser(ForgotPasswordEvent $forgotPasswordEvent): void
    {
        $messsage = new Swift_Message('Forgot password', sprintf(
            'You dumb human, click here: <a href="%s">I forgot it, I\'am sorry mr. computer, it will never happen again.</a>',
            $this->urlGenerator->generate('app_security_change_password', [
                'token' => $forgotPasswordEvent->getUser()->getResetPasswordToken(),
            ], UrlGeneratorInterface::ABSOLUTE_URL)
        ));

        $messsage->setContentType('text/html');

        $messsage->setFrom('dumb_human_notifier@carmeet.internal');
        $messsage->setTo($forgotPasswordEvent->getUser()->getEmail());

        $this->mailer->send($messsage);
    }
}
