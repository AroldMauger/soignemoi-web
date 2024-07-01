<?php

namespace App\Form;

use App\Entity\Doctors;
use App\Entity\Slot;
use App\Entity\Stays;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StaysType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('entrydate', null, [
                'widget' => 'single_text'
            ])
            ->add('leavingdate', null, [
                'widget' => 'single_text'
            ])
            ->add('speciality')
            ->add('reason')
            ->add('status')
            ->add('doctor', EntityType::class, [
                'class' => Doctors::class,
'choice_label' => 'id',
            ])
            ->add('slot', EntityType::class, [
                'class' => Slot::class,
'choice_label' => 'id',
            ])
            ->add('user', EntityType::class, [
                'class' => Users::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stays::class,
        ]);
    }
}
