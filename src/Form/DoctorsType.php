<?php

namespace App\Form;

use App\Entity\Doctors;
use App\Entity\Specialities;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class DoctorsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname')
            ->add('firstname')
            ->add('speciality', EntityType::class, [
                'class' => Specialities::class,
                'choice_label' => 'name', // Utilisez le nom de la spécialité comme libellé
                'placeholder' => 'Choisissez une spécialité',
                'query_builder' => function($repository) {
                    return $repository->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
                },
                'label' => 'Spécialité nécessaire',
            ])
            ->add('identification')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Doctors::class,
        ]);
    }
}
