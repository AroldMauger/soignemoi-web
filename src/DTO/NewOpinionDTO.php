<?php

namespace App\DTO;

class NewOpinionDTO
{
    private int $doctorId;
    private int $stayId;
    private string $date;
    private string $description;

    public function __construct(int $doctorId, int $stayId, string $date, string $description)
    {
        $this->doctorId = $doctorId;
        $this->stayId = $stayId;
        $this->date = $date;
        $this->description = $description;
    }

    public function getDoctorId(): int
    {
        return $this->doctorId;
    }

    public function getStayId(): int
    {
        return $this->stayId;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
