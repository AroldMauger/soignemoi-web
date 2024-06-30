<?php

namespace App\Controller;

use App\Entity\Doctors;
use App\Entity\Planning;
use App\Entity\Slot;
use App\Form\DoctorsType;
use App\Form\PlanningType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/admin/save-planning', name: 'admin_save_planning', methods: ['POST'])]
    public function savePlanning(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = $request->request->all();
        $doctorId = $data['doctorId'];
        $date = $data['date'];
        $slotsData = json_decode($data['slotsData'], true);

        $doctor = $entityManager->getRepository(Doctors::class)->find($doctorId);
        $planning = new Planning();
        $planning->setDoctor($doctor);
        $planning->setDate(new \DateTime($date));

        $entityManager->persist($planning);

        foreach ($slotsData as $slotData) {
            $slot = new Slot();
            $slot->setStarttime(new \DateTime($slotData['starttime']));
            $slot->setEndtime(new \DateTime($slotData['endtime']));
            $slot->setPlanning($planning);
            $slot->setIsbooked(false);  // Assurez-vous que isbooked est à false par défaut
            $entityManager->persist($slot);
        }

        $entityManager->flush();

        return $this->redirectToRoute('planning_success');
    }

    #[Route('/planning-success', name: 'planning_success')]
    public function planningSuccess(): Response
    {
        return $this->render('pages/planning-success.html.twig');
    }

    #[Route('/admin/planning', name: 'admin_planning')]
    public function showPlanning(Request $request): Response
    {
        $form = $this->createForm(PlanningType::class);

        // Pass your form and any other required data
        return $this->render('pages/admin-dashboard.html.twig', [
            'form' => $form->createView(),
            'specialities' => $this->getSpecialities(),
            'doctors' => $this->getDoctors(),
        ]);
    }

    private function getDoctors() {
        // Replace with your actual method for retrieving doctors
        return $this->getDoctors()->getRepository(Doctors::class)->findAll();
    }
}
