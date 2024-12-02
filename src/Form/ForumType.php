<?php

namespace App\Form;

use App\Entity\Forum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ForumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Forum Title', // Optional: Custom label
                'attr' => [
                    'placeholder' => 'Enter forum title',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description', // Optional: Custom label
                'attr' => [
                    'placeholder' => 'Enter forum description',
                    'rows' => 5, // Optional: Set textarea size
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Forum::class,
        ]);
    }
}
