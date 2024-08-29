<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Topic::class, mappedBy: 'category')]
    private Collection $topics;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    private ?User $author = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    // #[ORM\Column(length: 255, nullable: true)]
    // private ?string $image = null;

    // #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'categories')]
    // private ?User $author = null;

    // #[ORM\Column(type: 'datetime', nullable: true)]
    // private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->topics = new ArrayCollection();
        // $this->createdAt = new \DateTime();
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

    /**
     * @return Collection<int, Topic>
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic): static
    {
        if (!$this->topics->contains($topic)) {
            $this->topics->add($topic);
            $topic->setCategory($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): static
    {
        if ($this->topics->removeElement($topic)) {
            // set the owning side to null (unless already changed)
            if ($topic->getCategory() === $this) {
                $topic->setCategory(null);
            }
        }

        return $this;
    }

    // public function getImage(): ?string
    // {
    //     return $this->image;
    // }

    // public function setImage(?string $image): static
    // {
    //     $this->image = $image;

    //     return $this;
    // }

    // public function getAuthor(): ?User
    // {
    //     return $this->author;
    // }

    // public function setAuthor(?User $user): static
    // {
    //     $this->author = $user;

    //     return $this;
    // }

    // public function getCreatedAt(): ?\DateTimeInterface
    // {
    //     return $this->createdAt;
    // }

    // public function setCreatedAt(\DateTimeInterface $createdAt): static
    // {
    //     $this->createdAt = $createdAt;

    //     return $this;
    // }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
