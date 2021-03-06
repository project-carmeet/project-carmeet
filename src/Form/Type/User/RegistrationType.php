<?php

declare(strict_types=1);

namespace App\Form\Type\User;

use App\Model\Form\UserModel;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
        $builder->add('username', UniqueUsernameType::class);

        $builder->add('email', UniqueEmailType::class);

        $builder->add('password', RepeatedPasswordType::class);
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

    public function validateEmail(?string $email, ExecutionContextInterface $context): void
    {
        if (null !== $email && null !== $this->userRepository->findOneOrNullByEmail($email)) {
            $context->addViolation('User with this email already exists.');
        }
    }
}
