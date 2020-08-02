<?php

declare(strict_types=1);

namespace App\Form\Type\Event;

use App\Model\Form\EventModel;
use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

final class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'label' => 'event.name',
            'translation_domain' => 'form_types',
            'required' => false,
            'constraints' => [
                new NotBlank(),
            ],
        ]);

        $builder->add('description', TextareaType::class, [
            'label' => 'event.description',
            'translation_domain' => 'form_types',
            'required' => false,
        ]);

        $builder->add('date_from', DateTimeType::class, [
            'label' => 'event.date_from',
            'translation_domain' => 'form_types',
            'date_format' => 'yMd',
            'html5' => false,
            'required' => false,
            'data' => new DateTimeImmutable(),
            'years' => range((int)date('Y'), (int)date('Y') + 2),
            'constraints' => [
                new GreaterThan([
                    'value' => new DateTimeImmutable('today'),
                ]),
                new NotNull(),
            ],
        ]);

        $builder->add('date_until', DateTimeType::class, [
            'label' => 'event.date_until',
            'translation_domain' => 'form_types',
            'date_format' => 'yMd',
            'html5' => false,
            'required' => false,
            'data' => new DateTimeImmutable(),
            'years' => range((int)date('Y'), (int)date('Y') + 2),
            'constraints' => [
                new GreaterThan([
                    'value' => new DateTimeImmutable('today'),
                ]),
                new NotNull(),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', EventModel::class);
        $resolver->setDefault('label', false);
    }
}
