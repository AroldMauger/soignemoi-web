<?php

namespace App\DTO;

class ChangePrescriptionsPrescriptionDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $dosage,
        public readonly string $startDate,
        public readonly string $endDate,
    )
    {
    }
}