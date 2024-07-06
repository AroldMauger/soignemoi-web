<?php

namespace App\Entity;

use App\Repository\SpecialitiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SpecialitiesRepository::class)]
class Specialities
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\OneToMany(targetEntity: Reasons::class, mappedBy: 'speciality', cascade: ['PERSIST'])]
    private Collection $reasons;

    #[ORM\OneToMany(targetEntity: Doctors::class, mappedBy: 'speciality')]
    private Collection $doctors;

    public function __construct()
    {
        $this->reasons = new ArrayCollection();
        $this->doctors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getReasons(): Collection
    {
        return $this->reasons;
    }

    public function addReason(Reasons $reason): static
    {
        if (!$this->reasons->contains($reason)) {
            $this->reasons->add($reason);
            $reason->setSpeciality($this);
        }

        return $this;
    }

    public function removeReason(Reasons $reason): static
    {
        if ($this->reasons->removeElement($reason)) {
            if ($reason->getSpeciality() === $this) {
                $reason->setSpeciality(null);
            }
        }

        return $this;
    }

    public function getDoctors(): Collection
    {
        return $this->doctors;
    }

    public function addDoctor(Doctors $doctor): static
    {
        if (!$this->doctors->contains($doctor)) {
            $this->doctors->add($doctor);
            $doctor->setSpeciality($this);
        }

        return $this;
    }

    public function removeDoctor(Doctors $doctor): static
    {
        if ($this->doctors->removeElement($doctor)) {
            if ($doctor->getSpeciality() === $this) {
                $doctor->setSpeciality(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
