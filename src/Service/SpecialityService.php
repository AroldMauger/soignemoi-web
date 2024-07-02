<?php

namespace App\Service;

class SpecialityService
{
    public function getSpecialities(): array
    {
        return [
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
    }

    public function getReasonsBySpeciality(string $speciality): array
    {
        $reasons = [
            'Gynecologue' => [
                'Echographie du 1er trimestre' => 'echographie_1er_trimestre',
                '1ère consultation gynécologique' => 'consultation_gynécologique',
                // Ajoutez d'autres raisons pour cette spécialité
            ],
            'Cardiologue' => [
                'Examen du coeur' => 'examen_du_coeur',
                '1ère consultation cardiologie' => 'consultation_cardiologie',
                // Ajoutez d'autres raisons pour cette spécialité
            ],
            // Ajoutez d'autres spécialités et leurs raisons respectives
        ];

        return $reasons[$speciality] ?? [];
    }

}