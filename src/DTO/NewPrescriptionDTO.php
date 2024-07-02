<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints\NotBlank;

class NewPrescriptionDTO
{
    public function __construct(
        #[NotBlank]
        public readonly int $id,
        #[NotBlank]
        public readonly string $stayId,
    )
    {

    }
}