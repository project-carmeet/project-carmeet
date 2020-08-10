<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Entity\User;
use App\Event\User\ForgotPasswordEvent;
use App\Form\Type\User\ForgotPasswordType;
use App\Model\Form\ForgotPasswordModel;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use UnexpectedValueException;

final class ForgotPasswordController extends AbstractController
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    public function __construct(RequestStack $requestStack, UserRepository $userRepository, EventDispatcherInterface $dispatcher)
    {
        $this->requestStack = $requestStack;
        $this->userRepository = $userRepository;
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(): Response
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ForgotPasswordModel|mixed $data */
            $data = $form->getData();
            if (!$data instanceof ForgotPasswordModel) {
                throw new UnexpectedValueException('Invalid object received as form data.');
            }

            if (null === $data->getEmail()) {
                throw new UnexpectedValueException('Expected email to be set.');
            }

            $user = $this->userRepository->findOneOrNullByEmail($data->getEmail());
            if ($user instanceof User) {
                $this->dispatcher->dispatch(new ForgotPasswordEvent($user));
            }

            $this->addFlash('success', 'If an account for this email exists, you should receive an email soon.');
            $form = $this->createForm(ForgotPasswordType::class);
        }

        return $this->render('security/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
