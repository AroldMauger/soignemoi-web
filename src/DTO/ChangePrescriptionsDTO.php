<?php

namespace App\DTO;

class ChangePrescriptionsDTO
{
    public function __construct(
        /**
         * @var ChangePrescriptionsPrescriptionDTO[]
         */
        public readonly array $prescriptions,
    )
    {
    }
}