<?php

namespace App\Controller;

use App\Entity\Doctors;
use App\Form\DoctorsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin-dashboard', name: 'admin_dashboard')]
    public function adminDashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $specialities = $this->getSpecialities();
        ksort($specialities); // Tri des spécialités par ordre alphabétique

        return $this->render('pages/admin-dashboard.html.twig', [
            'specialities' => $specialities,
        ]);
    }

    #[Route('/add-doctor', name: 'doctor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $doctor = new Doctors();
        $form = $this->createForm(DoctorsType::class, $doctor);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($doctor);
            $entityManager->flush();

            return $this->redirectToRoute('doctor_success');
        }

        return $this->render('pages/add-doctors.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/add-doctor-success', name: 'doctor_success')]
    public function signupSuccess(): Response
    {
        return $this->render('pages/add-doctors-success.html.twig');
    }

    #[Route('/doctor-search', name: 'doctor_search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $entityManager): Response
    {
        $speciality = $request->query->get('speciality');
        $specialities = $this->getSpecialities();
        ksort($specialities); // Tri des spécialités par ordre alphabétique

        $doctors = [];
        if ($speciality) {
            $doctorRepository = $entityManager->getRepository(Doctors::class);
            $doctors = $doctorRepository->findBy(['speciality' => $speciality]);
        }

        return $this->render('pages/admin-dashboard.html.twig', [
            'doctors' => $doctors,
            'specialities' => $specialities,
        ]);
    }

    #[Route('/doctor-appointments/{id}', name: 'doctor_appointments', methods: ['GET'])]
    public function doctorAppointments(int $id, EntityManagerInterface $entityManager): Response
    {
        $doctor = $entityManager->getRepository(Doctors::class)->find($id);

        if (!$doctor) {
            throw $this->createNotFoundException('Médecin non trouvé');
        }

        // Implémentez ici la logique pour afficher les rendez-vous du médecin
        $appointments = []; // Remplacez ceci par votre logique de récupération des rendez-vous

        return $this->render('pages/doctor-appointments.html.twig', [
            'doctor' => $doctor,
            'appointments' => $appointments,
        ]);
    }

    private function getSpecialities(): array
    {
        return [
            'Médecin Généraliste' => 'medecin_generaliste',
            'Chirurgien' => 'chirurgien',
            'Gynécologue' => 'gynecologue',
            'Cardiologue' => 'cardiologue',
            'Pédiatre' => 'pediatre',
            'Dermatologue' => 'dermatologue',
            'Neurologue' => 'neurologue',
            'Orthopédiste' => 'orthopediste',
            'Endocrinologue' => 'endocrinologue',
            'Rhumatologue' => 'rhumatologue',
            'Oncologue' => 'oncologue',
            'Urologue' => 'urologue',
            'Ophtalmologiste' => 'ophtalmologiste',
            'Anesthésiste' => 'anesthesiste',
            'Gastro-entérologue' => 'gastro_enterologue',
            'Pneumologue' => 'pneumologue',
            'Psychiatre' => 'psychiatre',
        ];
    }
}
