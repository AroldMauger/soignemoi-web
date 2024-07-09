<?php

namespace App\Entity;

use App\Repository\DoctorsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DoctorsRepository::class)]
class Doctors implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['doctor:read', 'opinion:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['doctor:read', 'opinion:read'])]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['doctor:read', 'opinion:read'])]
    private ?string $firstname = null;

    #[ORM\ManyToOne(targetEntity: Specialities::class, inversedBy: 'doctors')]
    #[ORM\JoinColumn(name: 'speciality_id', referencedColumnName: 'id', nullable: false)]
    #[Groups(['doctor:read'])]
    private ?Specialities $speciality = null;

    #[ORM\Column(length: 255)]
    private ?string $identification = null;

    #[ORM\OneToMany(targetEntity: Planning::class, mappedBy: 'doctor')]
    private Collection $plannings;

    #[ORM\OneToMany(targetEntity: Stays::class, mappedBy: 'doctor')]
    private Collection $stays;

    #[ORM\OneToMany(targetEntity: Opinions::class, mappedBy: 'doctor')]
    private Collection $opinions;

    #[ORM\OneToMany(targetEntity: Slot::class, mappedBy: 'doctor')]
    private Collection $slots;

    public function __construct()
    {
        $this->plannings = new ArrayCollection();
        $this->stays = new ArrayCollection();
        $this->opinions = new ArrayCollection();
        $this->slots = new ArrayCollection();
    }

    #[Groups(['doctor:read'])]
    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['doctor:read'])]
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    #[Groups(['doctor:read'])]
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    #[Groups(['doctor:read'])]
    public function getSpeciality(): ?Specialities
    {
        return $this->speciality;
    }

    public function setSpeciality(?Specialities $speciality): static
    {
        $this->speciality = $speciality;

        return $this;
    }

    public function getIdentification(): ?string
    {
        return $this->identification;
    }

    public function setIdentification(string $identification): static
    {
        $this->identification = $identification;

        return $this;
    }

    public function getPlannings(): Collection
    {
        return $this->plannings;
    }

    public function addPlanning(Planning $planning): static
    {
        if (!$this->plannings->contains($planning)) {
            $this->plannings->add($planning);
            $planning->setDoctor($this);
        }

        return $this;
    }

    public function removePlanning(Planning $planning): static
    {
        if ($this->plannings->removeElement($planning)) {
            // set the owning side to null (unless already changed)
            if ($planning->getDoctor() === $this) {
                $planning->setDoctor(null);
            }
        }

        return $this;
    }

    public function getStays(): Collection
    {
        return $this->stays;
    }

    public function addStay(Stays $stay): static
    {
        if (!$this->stays->contains($stay)) {
            $this->stays->add($stay);
            $stay->setDoctor($this);
        }

        return $this;
    }

    public function removeStay(Stays $stay): static
    {
        if ($this->stays->removeElement($stay)) {
            // set the owning side to null (unless already changed)
            if ($stay->getDoctor() === $this) {
                $stay->setDoctor(null);
            }
        }

        return $this;
    }

    public function getAvailableSlots(): Collection
    {
        $slots = new ArrayCollection();

        foreach ($this->getPlannings() as $planning) {
            foreach ($planning->getSlots() as $slot) {
                if (!$slot->isBooked()) {
                    $slots->add($slot);
                }
            }
        }

        return $slots;
    }

    #[Groups(['doctor:read'])]
    public function getFullname(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getOpinions(): Collection
    {
        return $this->opinions;
    }

    public function addOpinion(Opinions $opinion): static
    {
        if (!$this->opinions->contains($opinion)) {
            $this->opinions->add($opinion);
            $opinion->setDoctor($this);
        }

        return $this;
    }

    public function removeOpinion(Opinions $opinion): static
    {
        if ($this->opinions->removeElement($opinion)) {
            // set the owning side to null (unless already changed)
            if ($opinion->getDoctor() === $this) {
                $opinion->setDoctor(null);
            }
        }

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->identification; // Ou toute autre propriété que vous utilisez pour stocker le mot de passe
    }

    public function setPassword(string $password): self
    {
        $this->identification = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_DOCTOR'];  // Ajoute un rôle par défaut pour les doctors
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUserIdentifier(): string
    {
        return $this->lastname;
    }

    public function eraseCredentials(): void
    {
        // Optionnel : efface les informations sensibles, ici rien à faire
    }
}
