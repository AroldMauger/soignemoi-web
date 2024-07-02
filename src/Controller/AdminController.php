<?php

namespace App\Controller;

use App\Entity\Doctors;
use App\Entity\Planning;
use App\Entity\Slot;
use App\Form\DoctorsType;
use App\Form\PlanningType;
use App\Service\SpecialityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private $specialityService;

    public function __construct(SpecialityService $specialityService)
    {
        $this->specialityService = $specialityService;
    }

    #[Route('/admin-dashboard', name: 'admin_dashboard')]
    public function adminDashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $specialities = $this->specialityService->getSpecialities();
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
        $specialities = $this->specialityService->getSpecialities();
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
            'specialities' => $this->specialityService->getSpecialities(),
            'doctors' => $this->getDoctors(),
        ]);
    }

    private function getDoctors() {
        // Replace with your actual method for retrieving doctors
        return $this->getDoctrine()->getRepository(Doctors::class)->findAll();
    }
}

