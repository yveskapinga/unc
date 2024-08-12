<?php

namespace App\Entity;

use App\Repository\ChannelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChannelRepository::class)]
class Channel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $isPrivate = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'channels')]
    private Collection $channelUser;

    public function __construct()
    {
        $this->channelUser = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isIsPrivate(): ?bool
    {
        return $this->isPrivate;
    }

    public function setIsPrivate(bool $isPrivate): static
    {
        $this->isPrivate = $isPrivate;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getChannelUser(): Collection
    {
        return $this->channelUser;
    }

    public function addChannelUser(User $channelUser): static
    {
        if (!$this->channelUser->contains($channelUser)) {
            $this->channelUser->add($channelUser);
        }

        return $this;
    }

    public function removeChannelUser(User $channelUser): static
    {
        $this->channelUser->removeElement($channelUser);

        return $this;
    }
}
