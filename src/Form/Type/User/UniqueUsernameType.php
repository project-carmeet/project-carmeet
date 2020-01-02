<?php

declare(strict_types=1);

namespace App\Form\Type\User;

use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class UniqueUsernameType extends AbstractType
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
        return UsernameType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
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
    }

    public function validateUsername(?string $username, ExecutionContextInterface $context): void
    {
        if (null !== $username && null !== $this->userRepository->findOneOrNullByUsername($username)) {
            $context->addViolation('Username is already taken.');
        }
    }
}
