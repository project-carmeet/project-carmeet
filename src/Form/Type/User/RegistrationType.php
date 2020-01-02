<?php

declare(strict_types=1);

namespace App\Form\Type\User;

use App\Model\Form\UserModel;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class RegistrationType extends AbstractType
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param array<mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('username', UsernameType::class, [
            'label' => 'Username',
            'constraints' => array_merge(
                UsernameType::getDefaultConstraints(),
                [
                    new NotBlank(),
                    new Callback([
                        'callback' => [$this, 'validateUsername'],
                    ]),
                ]
            ),
        ]);

        $builder->add('email', EmailType::class, [
            'label' => 'Email',
            'constraints' => [
                new NotBlank(),
                new Callback([
                    'callback' => [$this, 'validateEmail'],
                ]),
            ],
        ]);

        $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_options' => [
                'label' => 'Password',
                'constraints' => [
                    new NotBlank(),
                ],
            ],
            'second_options' => [
                'label' => 'Repeat password',
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserModel::class,
            'required' => false,
        ]);
    }

    public function validateUsername(?string $username, ExecutionContextInterface $context): void
    {
        if (null !== $username && null !== $this->userRepository->findOneOrNullByUsername($username)) {
            $context->addViolation('Username is already taken.');
        }
    }

    public function validateEmail(?string $email, ExecutionContextInterface $context): void
    {
        if (null !== $email && null !== $this->userRepository->findOneOrNullByEmail($email)) {
            $context->addViolation('User with this email already exists.');
        }
    }
}
