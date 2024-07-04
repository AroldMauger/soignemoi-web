<?php

namespace App\Form;

use App\Entity\Reasons;
use App\Entity\Specialities;
use App\Entity\Stays;
use App\Entity\Doctors;
use App\Entity\Slot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class StaysType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('entrydate', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'required' => true,
                'label' => "Date de début du séjour"
            ])
            ->add('leavingdate', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'required' => false,
                'label' => "Date de fin du séjour"
            ])
            ->add('speciality', EntityType::class, [
                'class' => Specialities::class,
                'choice_label' => 'name',
                'label' => 'Spécialité nécessaire',
                'attr' => ['class' => 'speciality-selector'],
                'placeholder' => 'Choisissez une spécialité',
            ])
            ->add('reason', EntityType::class, [
                'class' => Reasons::class,
                'choice_label' => 'name',
                'label' => 'Motif du séjour',
                'attr' => ['class' => 'reason-selector'],
                'placeholder' => 'Choisissez un motif',
            ])
            ->add('doctor', EntityType::class, [
                'class' => Doctors::class,
                'choice_label' => function (Doctors $doctor) {
                    return $doctor->getFirstname() . ' ' . $doctor->getLastname();
                },
                'label' => 'Nom du spécialiste',
                'attr' => ['class' => 'doctor-selector'],
                'placeholder' => 'Choisissez un médecin',
            ])
            ->add('slot', EntityType::class, [
                'class' => Slot::class,
                'choice_label' => function (Slot $slot) {
                    return $slot->getStarttime()->format('H:i') . ' - ' . $slot->getEndtime()->format('H:i');
                },
                'choice_value' => 'id',
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'attr' => ['class' => 'slot-selector'],
                'placeholder' => 'Choisissez un créneau',  // Placeholder pour le champ vide par défaut
            ])
            ->add('status', HiddenType::class, [
                'data' => 'en cours',
            ])
            ->add('user', HiddenType::class, [
                'data' => $options['user']->getId(),
                'mapped' => false,
            ])
            ->add('extendStay', ChoiceType::class, [
                'choices' => [
                    'Oui' => 'yes',
                    'Non' => 'no',
                ],
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,
                'label' => 'Souhaitez-vous prolonger votre séjour à l\'hôpital ?',
                'data' => 'no' // Définir la valeur par défaut à "Non"
            ])
            ->add('isEqualDates', HiddenType::class, [
                'mapped' => false,
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if (isset($data['extendStay']) && $data['extendStay'] === 'no' && isset($data['entrydate'])) {
                $data['leavingdate'] = $data['entrydate'];
                $data['isEqualDates'] = true;
                $event->setData($data);
            } else {
                $data['isEqualDates'] = false;
                $event->setData($data);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stays::class,
            'slots' => [],
            'user' => null,
        ]);
    }
}
