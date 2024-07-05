<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\OneToMany(targetEntity: Url::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $urls;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'followers')]
    #[ORM\JoinTable(
        name: 'user_following',
        joinColumns: [new ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'following_user_id', referencedColumnName: 'id')]
    )]
    private Collection $following;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'following')]
    private Collection $followers;

    #[ORM\ManyToMany(targetEntity: Url::class, inversedBy: 'likedBy')]
    #[ORM\JoinTable(
        name: 'user_likes',
        joinColumns: [new ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'url_id', referencedColumnName: 'id')]
    )]
    private Collection $likedUrls;

    public function __construct()
    {
        $this->following = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->urls = new ArrayCollection();
        $this->likedUrls = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }


    public function getUrls(): Collection
    {
        return $this->urls;
    }

    public function addUrl(Url $url): self
    {
        if (!$this->urls->contains($url)) {
            $this->urls->add($url);
            $url->setUser($this);
        }

        return $this;
    }

    public function removeUrl(Url $url): self
    {
        if ($this->urls->contains($url)) {
            $this->urls->removeElement($url);
            $url->setUser(null);
        }

        return $this;
    }

    public function getFollowing(): Collection
    {
        return $this->following;
    }

    public function follow(User $user): self
    {
        if (!$this->following->contains($user)) {
            $this->following->add($user);
            $user->addFollower($this);
        }

        return $this;
    }

    public function unfollow(User $user): self
    {
        if ($this->following->contains($user)) {
            $this->following->removeElement($user);
            $user->removeFollower($this);
        }

        return $this;
    }

    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(User $user): self
    {
        if (!$this->followers->contains($user)) {
            $this->followers->add($user);
        }

        return $this;
    }

    public function removeFollower(User $user): self
    {
        if ($this->followers->contains($user)) {
            $this->followers->removeElement($user);
        }

        return $this;
    }
    public function getLikedUrls(): Collection
    {
        return $this->likedUrls;
    }

    public function likeUrl(Url $url): self
    {
        if (!$this->likedUrls->contains($url)) {
            $this->likedUrls->add($url);
            $url->addLikedBy($this);
        }

        return $this;
    }

    public function unlikeUrl(Url $url): self
    {
        if ($this->likedUrls->contains($url)) {
            $this->likedUrls->removeElement($url);
            $url->removeLikedBy($this);
        }

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }
}
