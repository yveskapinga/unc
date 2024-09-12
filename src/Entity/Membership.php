<?php

namespace App\Entity;

use App\Repository\MembershipRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MembershipRepository::class)]
class Membership
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $level = null;

    #[ORM\Column(length: 255)]
    private ?string $fonction = null;

    #[ORM\ManyToOne(inversedBy: 'memberships')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Interfederation $interfederation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $membershipType = null;

    #[ORM\OneToOne(inversedBy: 'membership', cascade: ['persist', 'remove'])]
    private ?User $theUser = null;

    #[ORM\OneToOne(mappedBy: 'sif', cascade: ['persist', 'remove'])]
    private ?Interfederation $leader = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): static
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function getInterfederation(): ?Interfederation
    {
        return $this->interfederation;
    }

    public function setInterfederation(?Interfederation $interfederation): static
    {
        $this->interfederation = $interfederation;

        return $this;
    }

    public function changeInterfederation(?Interfederation $newInterfederation): static
    {
        $this->interfederation = $newInterfederation;

        return $this;
    }

        // Getters and setters for new fields
        public function getMembershipType(): ?string
        {
            return $this->membershipType;
        }
    
        public function setMembershipType(?string $membershipType): self
        {
            $this->membershipType = $membershipType;
    
            return $this;
        }

        public function getTheUser(): ?User
        {
            return $this->theUser;
        }

        public function setTheUser(?User $theUser): static
        {
            $this->theUser = $theUser;

            return $this;
        }

        public function __toString()
        {
            return 
                $this->getTheUser()->getUsername();
        }

        public function getLeader(): ?Interfederation
        {
            return $this->leader;
        }

        public function setLeader(Interfederation $leader): static
        {
            // set the owning side of the relation if necessary
            if ($leader->getSif() !== $this) {
                $leader->setSif($this);
            }

            $this->leader = $leader;

            return $this;
        }
}

