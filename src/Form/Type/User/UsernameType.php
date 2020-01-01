<?php

declare(strict_types=1);

namespace App\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

final class UsernameType extends AbstractType
{
    /**
     * @return array<int, Constraint>
     */
    public static function getDefaultConstraints(): array
    {
        return [
            new Regex([
                'pattern' => '/^[a-z0-9_\-.]*$/i',
                'message' => 'Username may only contain letters, numbers, underscores, dashes and dots.',
                'match' => true,
            ]),
            new Length([
                'min' => 3,
                'max' => 32,
            ]),
        ];
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => self::getDefaultConstraints(),
        ]);
    }
}
