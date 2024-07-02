<?php
namespace App\Form;

use App\Entity\Reasons;
use App\Entity\Specialities;
use App\Entity\Stays;
use App\Entity\Doctors;
use App\Entity\Slot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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

        $specs = [];
        foreach ($specialities as $speciality) {
            $specs[$speciality->getName()] = $speciality->getId();
        }

        $builder
            ->add('entrydate', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'required' => true,
                'label' => "Date de début du séjour"
            ])
            ->add('leavingdate', DateTimeType::class, [
                'widget' => 'single_text', // Utilisez un champ de type "single_text" pour un format de date compatible avec HTML5
                'html5' => true,
                'required' => false,
                'label' => "Date de fin du séjour"

            ])
            ->add('speciality', ChoiceType::class, [
                'label' => 'Spécialité nécessaire',
                'choices' => $specs,
                'attr' => ['class' => 'speciality-selector'],
            ])
            ->add('reason', ChoiceType::class, [
                'choices' => $reasons,
                'label' => 'Motif du séjour',
                'choice_label' => fn(Reasons $reason) => $reason->getName(),
                'attr' => ['class' => 'reason-selector'],
            ])
            ->add('doctor', EntityType::class, [
                'class' => Doctors::class,
                'choices' => $doctors,
                'choice_label' => function (Doctors $doctor) {
                    return $doctor->getFirstname() . ' ' . $doctor->getLastname();
                },
                'label' => 'Nom du spécialiste',
                'attr' => ['class' => 'doctor-selector'],
            ])
            ->add('slot', EntityType::class, [
                'class' => Slot::class,
                'choice_label' => function (Slot $slot) {
                    return $slot->getStarttime()->format('H:i');
                },
                'label' => false,
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
