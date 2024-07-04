<?php

// src/Form/SlotType.php

namespace App\Form;

use App\Entity\Planning;
use App\Entity\Slot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('starttime', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Début',
                'html5' => false,  // Désactiver HTML5
                'format' => 'yyyy-MM-dd HH:mm',  // Définir le format de date et heure
                'attr' => ['class' => 'datetimepicker'], // Ajouter une classe pour un éventuel date picker
            ])
            ->add('endtime', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Fin',
                'html5' => false,  // Désactiver HTML5
                'format' => 'yyyy-MM-dd HH:mm',  // Définir le format de date et heure
                'attr' => ['class' => 'datetimepicker'], // Ajouter une classe pour un éventuel date picker
            ])
            ->add('isbooked', CheckboxType::class, [
                'required' => false,
                'label' => 'Réservé',
                'data' => false,
            ])
            ->add('planning', EntityType::class, [
                'class' => Planning::class,
                'choice_label' => 'date',
                'label' => 'Planning',
                'placeholder' => 'Choisissez un planning',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Slot::class,
        ]);
    }
}
