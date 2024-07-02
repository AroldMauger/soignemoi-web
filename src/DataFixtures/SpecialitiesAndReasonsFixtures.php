<?php

namespace App\DataFixtures;

use App\Entity\Reasons;
use App\Entity\Specialities;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SpecialitiesAndReasonsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $specialities= [
            'Médecin Généraliste' => 'medecin_generaliste',
            'Chirurgien' => 'Chirurgien',
            'Gynecologue' => 'Gynecologue',
            'Cardiologue' => 'Cardiologue',
            'Pédiatre' => 'Pediatre',
            'Dermatologue' => 'Dermatologue',
            'Neurologue' => 'Neurologue',
            'Orthopédiste' => 'Orthopédiste',
            'Endocrinologue' => 'Endocrinologue',
            'Rhumatologue' => 'Rhumatologue',
            'Oncologue' => 'Oncologue',
            'Urologue' => 'Urologue',
            'Ophtalmologiste' => 'Ophtalmologiste',
            'Anesthésiste' => 'Anesthésiste',
            'Gastro-entérologue' => 'Gastro-entérologue',
            'Pneumologue' => 'Pneumologue',
            'Psychiatre' => 'Psychiatre',
        ];

        $reasons = [
            'Gynecologue' => [
                'Echographie du 1er trimestre' => 'Echographie du 1er trimestre',
                '1ère consultation gynécologique' => '1ère consultation gynécologique',
                // Ajoutez d'autres raisons pour cette spécialité
            ],
            'Cardiologue' => [
                'Examen du coeur' => 'Examen du coeur',
                '1ère consultation cardiologie' => '1ère consultation cardiologie',
                // Ajoutez d'autres raisons pour cette spécialité
            ],
            // Ajoutez d'autres spécialités et leurs raisons respectives
        ];

        foreach ($specialities as $name => $code) {
            $speciality = new Specialities();
            $speciality -> setName($name)
            ->setCode($code);
            if (isset($reasons[$code])) {
                foreach ($reasons[$code] as $reasonName => $reasonCode) {
                    $reason = new Reasons();
                    $reason -> setName($reasonName)
                        ->setCode($reasonCode);
                    $speciality ->addReason($reason);
                }
            }

            $manager->persist($speciality);
        }


        // $manager->persist($product);

        $manager->flush();
    }
}
