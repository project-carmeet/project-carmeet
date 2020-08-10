<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Factory\UserFactory;
use App\Form\Type\User\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

final class RegisterController extends AbstractController
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var UserFactory
     */
    protected $userFactory;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager, UserFactory $userFactory)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->userFactory = $userFactory;
    }

    public function __invoke(): Response
    {
        $form = $this->createForm(RegistrationType::class);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userFactory->createFromUserModel($form->getData());
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'New user has been registered with uuid ' . $user->getId() . '.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
