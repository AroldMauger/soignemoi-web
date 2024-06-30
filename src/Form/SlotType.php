<?php

// src/Form/SlotType.php

namespace App\Form;

use App\Entity\Slot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('starttime', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Début'
            ])
            ->add('endtime', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Fin'
            ])
            ->add('isbooked', CheckboxType::class, [
                'required' => false,
                'label' => 'Réservé',
                'data' => false,
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Slot::class,
        ]);
    }
}
