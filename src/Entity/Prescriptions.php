<?php

namespace App\Entity;

use App\Repository\PrescriptionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrescriptionsRepository::class)]
class Prescriptions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'prescriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stays $stay = null;

    /**
     * @var Collection<int, Medicines>
     */
    #[ORM\OneToMany(targetEntity: Medicines::class, mappedBy: 'prescrition', cascade: ['PERSIST', 'REMOVE'])]
    private Collection $medicines;

    public function __construct()
    {
        $this->medicines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStay(): ?Stays
    {
        return $this->stay;
    }

    public function setStay(?Stays $stay): static
    {
        $this->stay = $stay;

        return $this;
    }

    /**
     * @return Collection<int, Medicines>
     */
    public function getMedicines(): Collection
    {
        return $this->medicines;
    }

    public function addMedicine(Medicines $medicine): static
    {
        if (!$this->medicines->contains($medicine)) {
            $this->medicines->add($medicine);
            $medicine->setPrescrition($this);
        }

        return $this;
    }

    public function removeMedicine(Medicines $medicine): static
    {
        if ($this->medicines->removeElement($medicine)) {
            // set the owning side to null (unless already changed)
            if ($medicine->getPrescrition() === $this) {
                $medicine->setPrescrition(null);
            }
        }

        return $this;
    }
}
