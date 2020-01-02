<?php

declare(strict_types=1);

namespace App\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class RepeatedPasswordType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
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

    public function getParent(): string
    {
        return RepeatedType::class;
    }
}
