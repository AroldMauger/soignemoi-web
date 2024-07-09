<?php

namespace App\DTO;

class OpinionResponseDTO
{
    private int $id;
    private int $doctorId;
    private int $stayId;
    private string $date;
    private string $description;

    public function __construct(int $id, int $doctorId, int $stayId, string $date, string $description)
    {
        $this->id = $id;
        $this->doctorId = $doctorId;
        $this->stayId = $stayId;
        $this->date = $date;
        $this->description = $description;
    }

    public function getId(): int
    {
        return $this->id;
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
