<?php

declare(strict_types=1);

namespace App\Controller;

use App\Event\User\ForgotPasswordEvent;
use App\Event\User\ResetPasswordEvent;
use App\Factory\UserFactory;
use App\Form\Type\User\ForgotPasswordType;
use App\Form\Type\User\RegistrationType;
use App\Form\Type\User\RepeatedPasswordType;
use App\Model\Form\ForgotPasswordModel;
use App\Repository\UserRepository;
use App\Service\Authentication\ResetTokenValidator;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use UnexpectedValueException;

final class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('app_homepage');
        }

        $lastUsername = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserFactory $userFactory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RegistrationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userFactory->createFromUserModel($form->getData());
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'New user has been registered with uuid ' . $user->getId() . '.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/forgot-password", name="app_security_forgot_password")
     */
    public function forgotPassword(Request $request, EventDispatcherInterface $eventDispatcher, UserRepository $userRepository): Response
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ForgotPasswordModel $data */
            $data = $form->getData();

            $user = $userRepository->findOneOrNullByEmail($data->getEmail());
            if (null !== $user) {
                $eventDispatcher->dispatch(new ForgotPasswordEvent($user));
            }

            $this->addFlash('success', 'If an account for this email exists, you should receive an email soon.');
            $form = $this->createForm(ForgotPasswordType::class);
        }

        return $this->render('security/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/change-password/{token}", name="app_security_change_password")
     */
    public function changePassword(
        string $token,
        Request $request,
        EventDispatcherInterface $eventDispatcher,
        UserRepository $userRepository,
        ResetTokenValidator $resetTokenValidator
    ): Response {
        $user = $userRepository->findOneOrNullByForgotPasswordToken($token);
        if (null === $user) {
            throw $this->createNotFoundException(sprintf('No reset user found by token "%s".', $token));
        }

        if (!$user->hasResetPasswordToken()) {
            throw new BadRequestHttpException('No valid token present on the user.');
        }

        if ($resetTokenValidator->isExpired($user)) {
            return $this->render('security/expired_reset_token.html.twig');
        }

        $form = $this->createForm(RepeatedPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->getData();
            if (!is_string($password)) {
                throw new UnexpectedValueException('Expected password to be a string.');
            }

            $eventDispatcher->dispatch(new ResetPasswordEvent($user, $password));

            $this->addFlash('success', 'Updated password.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
