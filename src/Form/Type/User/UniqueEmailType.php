<?php

declare(strict_types=1);

namespace App\Form\Type\User;

use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class UniqueEmailType extends AbstractType
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getParent(): string
    {
        return EmailType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'Email',
            'constraints' => [
                new NotBlank(),
                new Callback([
                    'callback' => [$this, 'validateEmail'],
                ]),
            ],
        ]);
    }

    public function validateEmail(?string $email, ExecutionContextInterface $context): void
    {
        if (null !== $email && null !== $this->userRepository->findOneOrNullByEmail($email)) {
            $context->addViolation('User with this email already exists.');
        }
    }
}
