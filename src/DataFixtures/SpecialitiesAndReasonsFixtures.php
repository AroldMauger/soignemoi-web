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
            'Médecin Généraliste' => [
                'Consultation générale' => 'consultation_generale',
                'Vaccination' => 'vaccination',
                'Contrôle annuel' => 'controle_annuel',
                'Maladies courantes' => 'maladies_courantes',
            ],
            'Chirurgien' => [
                'Consultation pré-opératoire' => 'consultation_pre_operatoire',
                'Consultation post-opératoire' => 'consultation_post_operatoire',
                'Intervention chirurgicale' => 'intervention_chirurgicale',
                'Suivi des plaies' => 'suivi_des_plaies',
            ],
            'Gynecologue' => [
                'Echographie du 1er trimestre' => 'echographie_1er_trimestre',
                '1ère consultation gynécologique' => '1ere_consultation_gynecologique',
                'Suivi de grossesse' => 'suivi_de_grossesse',
                'Consultation pour contraception' => 'consultation_contraception',
            ],
            'Cardiologue' => [
                'Examen du coeur' => 'examen_du_coeur',
                '1ère consultation cardiologie' => '1ere_consultation_cardiologie',
                'Suivi hypertension' => 'suivi_hypertension',
                'Bilan cardiaque' => 'bilan_cardiaque',
            ],
            'Pédiatre' => [
                'Consultation pédiatrique' => 'consultation_pediatrique',
                'Vaccinations enfant' => 'vaccinations_enfant',
                'Suivi de croissance' => 'suivi_de_croissance',
                'Maladies infantiles' => 'maladies_infantiles',
            ],
            'Dermatologue' => [
                'Consultation dermatologique' => 'consultation_dermatologique',
                'Examen de la peau' => 'examen_de_la_peau',
                'Traitement de l\'acné' => 'traitement_acne',
                'Suivi des grains de beauté' => 'suivi_grains_de_beaute',
            ],
            'Neurologue' => [
                'Consultation neurologie' => 'consultation_neurologie',
                'Bilan neuropsychologique' => 'bilan_neuropsychologique',
                'Suivi épilepsie' => 'suivi_epilepsie',
                'Traitement des migraines' => 'traitement_migraines',
            ],
            'Orthopédiste' => [
                'Consultation orthopédique' => 'consultation_orthopedique',
                'Suivi post-fracture' => 'suivi_post_fracture',
                'Traitement des entorses' => 'traitement_entorses',
                'Chirurgie orthopédique' => 'chirurgie_orthopedique',
            ],
            'Endocrinologue' => [
                'Consultation endocrinologique' => 'consultation_endocrinologique',
                'Suivi diabète' => 'suivi_diabete',
                'Bilan thyroïdien' => 'bilan_thyroidien',
                'Traitement des troubles hormonaux' => 'traitement_troubles_hormonaux',
            ],
            'Rhumatologue' => [
                'Consultation rhumatologique' => 'consultation_rhumatologique',
                'Suivi arthrose' => 'suivi_arthrose',
                'Traitement des rhumatismes' => 'traitement_rhumatismes',
                'Infiltration articulaire' => 'infiltration_articulaire',
            ],
            'Oncologue' => [
                'Consultation oncologique' => 'consultation_oncologique',
                'Suivi cancer' => 'suivi_cancer',
                'Chimiothérapie' => 'chimiotherapie',
                'Radiothérapie' => 'radiotherapie',
            ],
            'Urologue' => [
                'Consultation urologique' => 'consultation_urologique',
                'Bilan prostatique' => 'bilan_prostatique',
                'Traitement des infections urinaires' => 'traitement_infections_urinaires',
                'Chirurgie urologique' => 'chirurgie_urologique',
            ],
            'Ophtalmologiste' => [
                'Consultation ophtalmologique' => 'consultation_ophtalmologique',
                'Correction de la vue' => 'correction_vue',
                'Dépistage glaucome' => 'depistage_glaucome',
                'Chirurgie des yeux' => 'chirurgie_yeux',
            ],
            'Anesthésiste' => [
                'Consultation anesthésie' => 'consultation_anesthesie',
                'Préparation à la chirurgie' => 'preparation_chirurgie',
                'Gestion de la douleur' => 'gestion_douleur',
                'Suivi post-anesthésie' => 'suivi_post_anesthesie',
            ],
            'Gastro-entérologue' => [
                'Consultation gastro-entérologique' => 'consultation_gastro_enterologique',
                'Endoscopie' => 'endoscopie',
                'Traitement des troubles digestifs' => 'traitement_troubles_digestifs',
                'Suivi maladie de Crohn' => 'suivi_maladie_de_crohn',
            ],
            'Pneumologue' => [
                'Consultation pneumologique' => 'consultation_pneumologique',
                'Bilan respiratoire' => 'bilan_respiratoire',
                'Traitement de l\'asthme' => 'traitement_asthme',
                'Suivi des maladies pulmonaires' => 'suivi_maladies_pulmonaires',
            ],
            'Psychiatre' => [
                'Consultation psychiatrique' => 'consultation_psychiatrique',
                'Suivi dépression' => 'suivi_depression',
                'Traitement des troubles anxieux' => 'traitement_troubles_anxieux',
                'Thérapie comportementale et cognitive' => 'therapie_comportementale_et_cognitive',
            ],
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

        $manager->flush();
    }
}
