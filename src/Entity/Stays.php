<?php

namespace App\Entity;

use App\Repository\StaysRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StaysRepository::class)]
class Stays
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('stays_list')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups('stays_list')]
    private ?\DateTimeInterface $entrydate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups('stays_list')]
    private ?\DateTimeInterface $leavingdate = null;

    #[ORM\ManyToOne()]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('stays_list')]
    private ?Specialities $speciality = null;

    #[ORM\ManyToOne()]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('stays_list')]
    private ?Reasons $reason = null;

    #[ORM\ManyToOne(inversedBy: 'stays')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('stays_list')]
    private ?Doctors $doctor = null;

    #[ORM\ManyToOne(inversedBy: 'stays')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('stays_list')]
    private ?Slot $slot = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'stays')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('stays_list')]
    private ?Users $user = null;

    #[ORM\Column(length: 255)]
    #[Groups('stays_list')]
    private ?string $status = null;

    #[ORM\OneToMany(targetEntity: Opinions::class, mappedBy: 'stay')]
    #[Groups('stays_list')]
    private Collection $opinions;

    #[ORM\OneToMany(targetEntity: Prescriptions::class, mappedBy: 'stay')]
    #[Groups('stays_list')]
    private Collection $prescriptions;

    public function __construct()
    {
        $this->opinions = new ArrayCollection();
        $this->prescriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntrydate(): ?\DateTimeInterface
    {
        return $this->entrydate;
    }

    public function setEntrydate(\DateTimeInterface $entrydate): static
    {
        $this->entrydate = $entrydate;

        return $this;
    }

    public function getLeavingdate(): ?\DateTimeInterface
    {
        return $this->leavingdate;
    }

    public function setLeavingdate(\DateTimeInterface $leavingdate): static
    {
        $this->leavingdate = $leavingdate;

        return $this;
    }

    public function getSpeciality(): ?Specialities
    {
        return $this->speciality;
    }

    public function setSpeciality(Specialities $speciality): static
    {
        $this->speciality = $speciality;

        return $this;
    }

    public function getReason(): ?Reasons
    {
        return $this->reason;
    }

    public function setReason(Reasons $reason): static
    {
        $this->reason = $reason;

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

    public function getSlot(): ?Slot
    {
        return $this->slot;
    }

    public function setSlot(?Slot $slot): static
    {
        $this->slot = $slot;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getOpinions(): Collection
    {
        return $this->opinions;
    }

    public function addOpinion(Opinions $opinion): static
    {
        if (!$this->opinions->contains($opinion)) {
            $this->opinions->add($opinion);
            $opinion->setStay($this);
        }

        return $this;
    }

    public function removeOpinion(Opinions $opinion): static
    {
        if ($this->opinions->removeElement($opinion)) {
            if ($opinion->getStay() === $this) {
                $opinion->setStay(null);
            }
        }

        return $this;
    }

    public function getPrescriptions(): Collection
    {
        return $this->prescriptions;
    }

    public function addPrescription(Prescriptions $prescription): static
    {
        if (!$this->prescriptions->contains($prescription)) {
            $this->prescriptions->add($prescription);
            $prescription->setStay($this);
        }

        return $this;
    }

    public function removePrescription(Prescriptions $prescription): static
    {
        if ($this->prescriptions->removeElement($prescription)) {
            if ($prescription->getStay() === $this) {
                $prescription->setStay(null);
            }
        }

        return $this;
    }

    private $isEqualDates;

    public function getIsEqualDates(): ?bool
    {
        return $this->isEqualDates;
    }

    public function setIsEqualDates(?bool $isEqualDates): self
    {
        $this->isEqualDates = $isEqualDates;

        return $this;
    }
}
