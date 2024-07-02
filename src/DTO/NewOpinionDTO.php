<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints\NotBlank;

class NewOpinionDTO
{
    public function __construct(
        #[NotBlank]
        public readonly int $id,
        #[NotBlank]
        public readonly int $doctorId,
        #[NotBlank]
        public readonly int $stayId,
        #[NotBlank]
        public readonly string $date,
        #[NotBlank]
        public readonly string $description,
    )
    {

    }
}