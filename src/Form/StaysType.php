<?php
namespace App\Form;

use App\Entity\Stays;
use App\Entity\Doctors;
use App\Entity\Slot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class StaysType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $specialities = $options['specialities'];
        $reasons = $options['reasons'];
        $doctors = $options['doctors'];

        $builder
            ->add('entrydate', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Entrée :',
            ])
            ->add('leavingdate', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Sortie :',
                'required' => false,
            ])
            ->add('speciality', ChoiceType::class, [
                'choices' => array_flip($specialities),
                'label' => 'Spécialité nécessaire',
                'attr' => ['class' => 'speciality-selector'],
            ])
            ->add('reason', ChoiceType::class, [
                'choices' => array_flip($reasons),
                'label' => 'Motif du séjour',
                'attr' => ['class' => 'reason-selector'],
            ])
            ->add('doctor', EntityType::class, [
                'class' => Doctors::class,
                'choices' => $doctors,
                'choice_label' => function (Doctors $doctor) {
                    return $doctor->getFirstname() . ' ' . $doctor->getLastname();
                },
                'label' => 'Nom du médecin / spécialiste médical',
                'attr' => ['class' => 'doctor-selector'],
            ])
            ->add('slot', EntityType::class, [
                'class' => Slot::class,
                'choice_label' => function (Slot $slot) {
                    return $slot->getStarttime()->format('H:i');
                },
                'label' => 'Heure de RDV (choisir le créneau qui vous convient)',
                'choices' => [],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stays::class,
            'specialities' => [],
            'reasons' => [],
            'doctors' => [],
        ]);
    }
}
