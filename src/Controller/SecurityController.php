<?php

declare(strict_types=1);

namespace App\Controller;

use App\Event\User\ForgotPasswordEvent;
use App\Factory\UserFactory;
use App\Form\Type\User\ForgotPasswordType;
use App\Form\Type\User\RegistrationType;
use App\Model\Form\ForgotPasswordModel;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

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
    public function changePassword(string $token, UserRepository $userRepository): Response
    {
        dd($token, $userRepository->findOneOrNullByForgotPasswordToken($token));

        // TODO: implement this method to acctually show the form and handle the change

        return new Response('TODO');
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
