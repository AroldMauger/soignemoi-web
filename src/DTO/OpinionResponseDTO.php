<?php

namespace App\DTO;

class OpinionResponseDTO
{
    public int $id;
    public int $doctorId;
    public int $stayId;
    public string $date;
    public string $description;

    public function __construct(int $id, int $doctorId, int $stayId, string $date, string $description)
    {
        $this->id = $id;
        $this->doctorId = $doctorId;
        $this->stayId = $stayId;
        $this->date = $date;
        $this->description = $description;
    }
}
