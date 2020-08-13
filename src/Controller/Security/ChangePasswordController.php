<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Entity\User;
use App\Event\User\ResetPasswordEvent;
use App\Form\Type\User\RepeatedPasswordType;
use App\Repository\UserRepository;
use App\Service\Authentication\ResetTokenValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use UnexpectedValueException;

final class ChangePasswordController extends AbstractController
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var ResetTokenValidator
     */
    protected $resetTokenValidator;

    public function __construct(
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        UserRepository $userRepository,
        ResetTokenValidator $resetTokenValidator
    ) {
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
        $this->userRepository = $userRepository;
        $this->resetTokenValidator = $resetTokenValidator;
    }

    public function __invoke(string $token): Response
    {
        $user = $this->userRepository->findOneOrNullByForgotPasswordToken($token);
        if (!$user instanceof User) {
            throw $this->createNotFoundException(sprintf('No reset user found by token "%s".', $token));
        }

        if (!$user->hasResetPasswordToken()) {
            throw new BadRequestHttpException('No valid token present on the user.');
        }

        if ($this->resetTokenValidator->isExpired($user)) {
            return $this->render('security/expired_reset_token.html.twig');
        }

        $form = $this->createForm(RepeatedPasswordType::class);
        $form->handleRequest($this->requestStack->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string|mixed $password */
            $password = $form->getData();
            if (!is_string($password)) {
                throw new UnexpectedValueException('Expected password to be a string.');
            }

            $this->eventDispatcher->dispatch(new ResetPasswordEvent($user, $password));

            $this->addFlash('success', 'Updated password.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
