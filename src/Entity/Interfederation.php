<?php

namespace App\Entity;

use App\Repository\InterfederationRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: InterfederationRepository::class)]
class Interfederation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $designation = null;

    // #[ORM\OneToOne(inversedBy: 'interfederation', cascade: ['persist'])]
    // #[ORM\JoinColumn(nullable: true)]
    // private ?Membership $sif = null;

    #[ORM\OneToOne(inversedBy: 'interfederation', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Address $siege = null;

    #[ORM\OneToMany(mappedBy: 'interfederation', targetEntity: Membership::class)]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $memberships;

    #[ORM\OneToOne(inversedBy: 'leader', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Membership $sif = null;

    public function __construct()
    {
        $this->memberships = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): static
    {
        $this->designation = $designation;

        return $this;
    }

    // public function getSif(): ?User
    // {
    //     return $this->sif;
    // }

    // public function setSif(Membership $sif): static
    // {
    //     $this->sif = $sif;

    //     return $this;
    // }

    public function getSiege(): ?Address
    {
        return $this->siege;
    }

    public function setSiege(Address $siege): static
    {
        $this->siege = $siege;

        return $this;
    }

    /**
     * @return Collection<int, Membership>
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    public function addMembership(Membership $membership): static
    {
        if (!$this->memberships->contains($membership)) {
            $this->memberships->add($membership);
            $membership->setInterfederation($this);
        }

        return $this;
    }

    public function removeMembership(Membership $membership): static
    {
        if ($this->memberships->removeElement($membership)) {
            // set the owning side to null (unless already changed)
            if ($membership->getInterfederation() === $this) {
                $membership->setInterfederation(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->designation;
    }

    public function getSif(): ?Membership
    {
        return $this->sif;
    }

    public function setSif(Membership $sif): static
    {
        $this->sif = $sif;

        return $this;
    }
}

