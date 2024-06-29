<?php

namespace App\Form;

use App\Entity\Doctors;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DoctorsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname')
            ->add('firstname')
            ->add('speciality', ChoiceType::class, [
                'choices'  => $this->getSpecialities(),
                'placeholder' => 'Choisissez une spécialité',
                'required' => true,
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

    private function getSpecialities(): array
    {
        $specialities = [
            'Médecin Généraliste' => 'Medecin_generaliste',
            'Chirurgien' => 'Chirurgien',
            'Gynécologue' => 'Gynecologue',
            'Cardiologue' => 'Cardiologue',
            'Pédiatre' => 'Pediatre',
            'Dermatologue' => 'Dermatologue',
            'Neurologue' => 'Neurologue',
            'Orthopédiste' => 'Orthopediste',
            'Endocrinologue' => 'Endocrinologue',
            'Rhumatologue' => 'Rhumatologue',
            'Oncologue' => 'Oncologue',
            'Urologue' => 'Urologue',
            'Ophtalmologiste' => 'Ophtalmologiste',
            'Anesthésiste' => 'Anesthesiste',
            'Gastro-entérologue' => 'Gastro_enterologue',
            'Pneumologue' => 'Pneumologue',
            'Psychiatre' => 'Psychiatre',
        ];

        // On trie les spécialités par ordre alphabétique
        ksort($specialities);

        return $specialities;
    }
}
