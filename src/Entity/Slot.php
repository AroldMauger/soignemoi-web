<?php

namespace App\Entity;

use App\Repository\SlotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SlotRepository::class)]
class Slot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Planning::class, inversedBy: 'slots')]
    private ?Planning $planning = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $starttime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $endtime = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isbooked = false;

    #[ORM\OneToMany(targetEntity: Stays::class, mappedBy: 'slot')]
    private Collection $stays;

    #[ORM\ManyToOne(targetEntity: Doctors::class, inversedBy: 'slots')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Doctors $doctor = null;

    public function __construct()
    {
        $this->stays = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlanning(): ?Planning
    {
        return $this->planning;
    }

    public function setPlanning(?Planning $planning): static
    {
        $this->planning = $planning;
        return $this;
    }

    public function getStarttime(): ?\DateTimeInterface
    {
        return $this->starttime;
    }

    public function setStarttime(\DateTimeInterface $starttime): static
    {
        $this->starttime = $starttime;
        return $this;
    }

    public function getEndtime(): ?\DateTimeInterface
    {
        return $this->endtime;
    }

    public function setEndtime(\DateTimeInterface $endtime): static
    {
        $this->endtime = $endtime;
        return $this;
    }

    public function isBooked(): bool
    {
        return $this->isbooked;
    }

    public function setIsBooked(bool $isbooked): static
    {
        $this->isbooked = $isbooked;
        return $this;
    }

    public function getDoctor(): ?Doctors
    {
        return $this->doctor;
    }

    public function setDoctor(?Doctors $doctor): static
    {
        $this->doctor = $doctor;
        return $this;
    }

    public function getFormattedTime(): string
    {
        return $this->starttime->format('H:i') . ' - ' . $this->endtime->format('H:i');
    }

    public function getStays(): Collection
    {
        return $this->stays;
    }

    public function addStay(Stays $stay): static
    {
        if (!$this->stays->contains($stay)) {
            $this->stays->add($stay);
            $stay->setSlot($this);
        }
        return $this;
    }

    public function removeStay(Stays $stay): static
    {
        if ($this->stays->removeElement($stay)) {
            if ($stay->getSlot() === $this) {
                $stay->setSlot(null);
            }
        }
        return $this;
    }
    public function __toString(): string
    {
        return $this->starttime->format('H:i') . ' - ' . $this->endtime->format('H:i');
    }

}
