<?php

namespace App\Entity;

use App\Repository\SpecialitiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpecialitiesRepository::class)]
class Specialities
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    /**
     * @var Collection<int, Reasons>
     */
    #[ORM\OneToMany(targetEntity: Reasons::class, mappedBy: 'speciality', cascade: ["PERSIST"])]
    private Collection $reasons;

    public function __construct()
    {
        $this->reasons = new ArrayCollection();
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

    /**
     * @return Collection<int, Reasons>
     */
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
            // set the owning side to null (unless already changed)
            if ($reason->getSpeciality() === $this) {
                $reason->setSpeciality(null);
            }
        }

        return $this;
    }
}
