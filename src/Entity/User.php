<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte lié à cette adresse mail')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $joinedAt = null;

    #[ORM\Column]
    private ?bool $isActive = false;

    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'author')]
    private Collection $posts;

    #[ORM\OneToMany(targetEntity: Topic::class, mappedBy: 'author')]
    private Collection $topics;

    #[ORM\OneToMany(targetEntity: Poll::class, mappedBy: 'author')]
    private Collection $polls;

    #[ORM\OneToMany(targetEntity: Document::class, mappedBy: 'author')]
    private Collection $documents;

    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'organizer')]
    private Collection $events;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'sender')]
    private Collection $messages;

    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'theUser')]
    private Collection $notifications;

    #[ORM\ManyToMany(targetEntity: Channel::class, mappedBy: 'channelUser')]
    private Collection $channels;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'participant')]
    private Collection $eventsParticipants;

    #[ORM\OneToOne(inversedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Address $address = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastActivityAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'author')]
    private Collection $categories;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'referrers')]
    private Collection $referredBy;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'referredBy')]
    private Collection $referrers;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nationality = null;

    #[ORM\OneToOne(mappedBy: 'theUser', cascade: ['persist', 'remove'])]
    private ?Membership $membership = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $referralCode = null;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->topics = new ArrayCollection();
        $this->polls = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->channels = new ArrayCollection();
        $this->eventsParticipants = new ArrayCollection();
        // $this->categories = new ArrayCollection();
        $this->referredBy = new ArrayCollection();
        $this->referrers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getJoinedAt(): ?\DateTimeInterface
    {
        return $this->joinedAt;
    }

    public function setJoinedAt(\DateTimeInterface $joinedAt): static
    {
        $this->joinedAt = $joinedAt;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

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
            $topic->setAuthor($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): static
    {
        if ($this->topics->removeElement($topic)) {
            // set the owning side to null (unless already changed)
            if ($topic->getAuthor() === $this) {
                $topic->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Poll>
     */
    public function getPolls(): Collection
    {
        return $this->polls;
    }

    public function addPoll(Poll $poll): static
    {
        if (!$this->polls->contains($poll)) {
            $this->polls->add($poll);
            $poll->setAuthor($this);
        }

        return $this;
    }

    public function removePoll(Poll $poll): static
    {
        if ($this->polls->removeElement($poll)) {
            // set the owning side to null (unless already changed)
            if ($poll->getAuthor() === $this) {
                $poll->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setAuthor($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): static
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getAuthor() === $this) {
                $document->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setOrganizer($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getOrganizer() === $this) {
                $event->setOrganizer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setSender($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getSender() === $this) {
                $message->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setTheUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getTheUser() === $this) {
                $notification->setTheUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Channel>
     */
    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function addChannel(Channel $channel): static
    {
        if (!$this->channels->contains($channel)) {
            $this->channels->add($channel);
            $channel->addChannelUser($this);
        }

        return $this;
    }

    public function removeChannel(Channel $channel): static
    {
        if ($this->channels->removeElement($channel)) {
            $channel->removeChannelUser($this);
        }

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEventsParticipants(): Collection
    {
        return $this->eventsParticipants;
    }

    public function addEventsParticipant(Event $eventsParticipant): static
    {
        if (!$this->eventsParticipants->contains($eventsParticipant)) {
            $this->eventsParticipants->add($eventsParticipant);
            $eventsParticipant->addParticipant($this);
        }

        return $this;
    }

    public function removeEventsParticipant(Event $eventsParticipant): static
    {
        if ($this->eventsParticipants->removeElement($eventsParticipant)) {
            $eventsParticipant->removeParticipant($this);
        }

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getLastActivityAt(): ?\DateTimeInterface
    {
        return $this->lastActivityAt;
    }

    public function setLastActivityAt(?\DateTimeInterface $lastActivityAt): static
    {
        $this->lastActivityAt = $lastActivityAt;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    //     /**
    //  * @return Collection<int, Category>
    //  */
    // public function getCategories(): Collection
    // {
    //     return $this->categories;
    // }

    // public function addCategory(Category $category): static
    // {
    //     if (!$this->categories->contains($category)) {
    //         $this->categories->add($category);
    //         $category->setAuthor($this);
    //     }

    //     return $this;
    // }

    // public function removeCategory(Category $category): static
    // {
    //     if ($this->categories->removeElement($category)) {
    //         // set the owning side to null (unless already changed)
    //         if ($category->getAuthor() === $this) {
    //             $category->setAuthor(null);
    //         }
    //     }

    //     return $this;
    // }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setAuthor($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getAuthor() === $this) {
                $category->setAuthor(null);
            }
        }

        return $this;
    }

    // public function getInterfederation(): ?Interfederation
    // {
    //     return $this->interfederation;
    // }

    // public function setInterfederation(Interfederation $interfederation): static
    // {
    //     // set the owning side of the relation if necessary
    //     if ($interfederation->getSif() !== $this) {
    //         $interfederation->setSif($this);
    //     }

    //     $this->interfederation = $interfederation;

    //     return $this;
    // }

    /**
     * @return Collection<int, self>
     */
    public function getReferredBy(): Collection
    {
        return $this->referredBy;
    }

    public function addReferredBy(self $referredBy): static
    {
        if (!$this->referredBy->contains($referredBy)) {
            $this->referredBy->add($referredBy);
        }

        return $this;
    }

    public function removeReferredBy(self $referredBy): static
    {
        $this->referredBy->removeElement($referredBy);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getReferrers(): Collection
    {
        return $this->referrers;
    }

    public function addReferrer(self $referrer): static
    {
        if (!$this->referrers->contains($referrer)) {
            $this->referrers->add($referrer);
            $referrer->addReferredBy($this);
        }

        return $this;
    }

    public function removeReferrer(self $referrer): static
    {
        if ($this->referrers->removeElement($referrer)) {
            $referrer->removeReferredBy($this);
        }

        return $this;
    }

        // Getters and setters for new fields
        public function getPhoneNumber(): ?string
        {
            return $this->phoneNumber;
        }
    
        public function setPhoneNumber(?string $phoneNumber): self
        {
            $this->phoneNumber = $phoneNumber;
    
            return $this;
        }
    
        public function getNationality(): ?string
        {
            return $this->nationality;
        }
    
        public function setNationality(?string $nationality): self
        {
            $this->nationality = $nationality;
    
            return $this;
        }

        public function getMembership(): ?Membership
        {
            return $this->membership;
        }

        public function setMembership(?Membership $membership): static
        {
            // unset the owning side of the relation if necessary
            if ($membership === null && $this->membership !== null) {
                $this->membership->setTheUser(null);
            }

            // set the owning side of the relation if necessary
            if ($membership !== null && $membership->getTheUser() !== $this) {
                $membership->setTheUser($this);
            }

            $this->membership = $membership;

            return $this;
        }

        public function getReferralCode(): ?string
        {
            return $this->referralCode;
        }

        public function setReferralCode(?string $referralCode): static
        {
            $this->referralCode = $referralCode;

            return $this;
        }

}
