<?php

namespace App\Controller;

use App\Entity\Doctors;
use App\Entity\Reasons;
use App\Entity\Specialities;
use App\Entity\Slot;
use App\Entity\Stays;
use App\Entity\Users;
use App\Form\StaysType;
use App\Repository\DoctorsRepository;
use App\Repository\ReasonsRepository;
use App\Repository\SpecialitiesRepository;
use App\Repository\SlotRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaysController extends AbstractController
{
    private SpecialitiesRepository $specialitiesRepository;
    private DoctorsRepository $doctorsRepository;
    private ReasonsRepository $reasonsRepository;
    private SlotRepository $slotRepository;
    private UsersRepository $usersRepository;

    public function __construct(
        SpecialitiesRepository $specialitiesRepository,
        DoctorsRepository $doctorsRepository,
        ReasonsRepository $reasonsRepository,
        SlotRepository $slotRepository,
        UsersRepository $usersRepository
    ) {
        $this->specialitiesRepository = $specialitiesRepository;
        $this->doctorsRepository = $doctorsRepository;
        $this->reasonsRepository = $reasonsRepository;
        $this->slotRepository = $slotRepository;
        $this->usersRepository = $usersRepository;
    }

    #[Route('/create-stay', name: 'create_stay', methods: ['GET'])]
    public function createStay(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException('Vous devez être connecté pour créer un séjour.');
        }

        return $this->redirectToRoute('add_stay');
    }

    // src/Controller/YourController.php
    #[Route('/add-stay', name: 'add_stay', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        UsersRepository $usersRepository,
        DoctorsRepository $doctorsRepository,
        SlotRepository $slotRepository,
        EntityManagerInterface $entityManager,
        Security $security
    ): Response
    {
        $userId = $request->query->getInt('user_id', 0);

        if ($userId > 0) {
            $user = $usersRepository->find($userId);
            if (!$user) {
                throw new \Exception('Utilisateur non trouvé.');
            }
        } else {
            $user = $security->getUser();
            if (!$user) {
                throw new \Exception('Utilisateur non authentifié.');
            }
        }

        $stay = new Stays();
        $slots = [];  // On ne définit pas ici les slots disponibles

        $form = $this->createForm(StaysType::class, $stay, [
            'user' => $user,
            'slots' => $slots,  // Passer les slots (vide par défaut)
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $slot = $data->getSlot();
            $data->setStatus('en cours');

            if (!$slot) {
                throw new \Exception('Le créneau sélectionné est invalide.');
            }

            if ($request->request->get('extend') === 'no') {
                $data->setLeavingdate($data->getEntrydate());
            }

            $data->setUser($user);
            $slot->setIsBooked(true);

            $entityManager->persist($data);
            $entityManager->persist($slot);
            $entityManager->flush();

            return $this->redirectToRoute('stay_success');
        }

        return $this->render('pages/add-stay.html.twig', [
            'form' => $form->createView(),
        ]);
    }




    #[Route('/stay-success', name: 'stay_success')]
    public function staySuccess(): Response
    {
        return $this->render('pages/add-stay-success.html.twig');
    }

    #[Route('/stay-search', name: 'stay_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $specialityId = $request->query->get('speciality');
        $speciality = $specialityId ? $this->specialitiesRepository->find($specialityId) : null;
        $doctors = [];
        $reasons = [];

        if ($speciality) {
            $doctors = $this->doctorsRepository->findBy(['speciality' => $speciality]);
            $reasons = $this->reasonsRepository->findBy(['speciality' => $speciality]);
        }

        return $this->json([
            'doctors' => array_map(fn(Doctors $doctor) => [
                'id' => $doctor->getId(),
                'firstname' => $doctor->getFirstname(),
                'lastname' => $doctor->getLastname()
            ], $doctors),
            'reasons' => array_map(fn(Reasons $reason) => [
                'id' => $reason->getId(),
                'name' => $reason->getName()
            ], $reasons),
        ]);
    }

    #[Route('/get-availability', name: 'get_availability', methods: ['POST'])]
    public function getAvailability(Request $request): JsonResponse
    {
        $doctorId = $request->request->get('doctor_id');
        $date = $request->request->get('date');

        $doctor = $this->doctorsRepository->find($doctorId);
        $dateTime = new \DateTime($date);

        if ($doctor) {
            $startOfDay = (clone $dateTime)->setTime(0, 0, 0);
            $endOfDay = (clone $dateTime)->setTime(23, 59, 59);

            $slots = $this->slotRepository->findAvailableSlots($doctor, $startOfDay, $endOfDay);
            $slotData = array_map(fn($slot) => [
                'id' => $slot->getId(),
                'starttime' => $slot->getStarttime()->format('H:i'),
                'endtime' => $slot->getEndtime()->format('H:i'),
            ], $slots);

            return $this->json([
                'slots' => $slotData,
            ]);
        }

        return $this->json(['error' => 'Invalid doctor or date'], 400);
    }
}
