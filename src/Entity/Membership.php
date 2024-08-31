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

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0', nullable: true)]
    private ?string $feeAmount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $feePaidAt = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $theUser = null;

    #[ORM\Column(length: 10)]
    private ?string $currency = null;

    #[ORM\Column(length: 255)]
    private ?string $fonction = null;

    #[ORM\ManyToOne(inversedBy: 'memberships')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Interfederation $interfederation = null;

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

    public function getFeeAmount(): ?string
    {
        return $this->feeAmount;
    }

    public function setFeeAmount(?string $feeAmount): static
    {
        $this->feeAmount = $feeAmount;

        return $this;
    }

    public function getFeePaidAt(): ?\DateTimeInterface
    {
        return $this->feePaidAt;
    }

    public function setFeePaidAt(?\DateTimeInterface $feePaidAt): static
    {
        $this->feePaidAt = $feePaidAt;

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

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

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
}

