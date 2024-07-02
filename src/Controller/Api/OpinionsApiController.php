<?php

namespace App\Controller\Api;

use App\DTO\NewOpinionDTO;
use App\Entity\Opinions;
use App\Repository\OpinionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/api/opinions")]
class OpinionsApiController extends AbstractController
{
    #[Route('/new_opinion', name:"api.new_opinion", methods: ["POST"])]
    public function newOpinion(OpinionsRepository $repo,
                                    #[MapRequestPayload]
                                    NewOpinionDTO $newOpinionDTO,
    )
    {
        $date = new \DateTimeImmutable($newOpinionDTO->date);
        $opinion = new Opinions();
        $opinion
            ->setDoctor($newOpinionDTO->doctorId)
            ->setStay($newOpinionDTO->stayId)
            ->setDate($date)
            ->setDescription($newOpinionDTO->description);

        $repo->save($opinion);
        return $this->json($opinion, Response::HTTP_CREATED);

    }
}