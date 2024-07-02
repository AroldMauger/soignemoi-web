<?php

namespace App\Entity;

use App\Repository\ReasonsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReasonsRepository::class)]
class Reasons
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reasons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Specialities $speciality = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSpeciality(): ?Specialities
    {
        return $this->speciality;
    }

    public function setSpeciality(?Specialities $speciality): static
    {
        $this->speciality = $speciality;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }
}
