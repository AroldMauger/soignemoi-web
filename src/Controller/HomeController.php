<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

use App\Form\NewAppointmentType;
use App\Repository\AppointmentsRepository;

class HomeController extends AbstractController {

    #[Route('/dashboard', name:"dashboard", methods: ['GET'])]
    public function dashboard()
    {
        return $this->render("pages/dashboard.html.twig");
    }


}