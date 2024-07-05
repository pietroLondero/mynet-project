<?php

namespace App\Entity;

use App\Repository\UrlRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UrlRepository::class)]
class Url
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'urls', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'urls', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'urls_tags')]
    private Collection $tags;

    #[ORM\Column()]
    private ?int $timeInsert = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'likedUrls')]
    private Collection $likedBy;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->likedBy = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getTimeInsert(): ?int
    {
        return $this->timeInsert;
    }

    public function setTimeInsert(int $timeInsert): self
    {
        $this->timeInsert = $timeInsert;
        return $this;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addUrl($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
            $tag->removeUrl($this);
        }

        return $this;
    }

    public function getLikedBy(): Collection
    {
        return $this->likedBy;
    }

    public function addLikedBy(User $user): self
    {
        if (!$this->likedBy->contains($user)) {
            $this->likedBy->add($user);
            $user->likeUrl($this);
        }

        return $this;
    }

    public function removeLikedBy(User $user): self
    {
        if ($this->likedBy->contains($user)) {
            $this->likedBy->removeElement($user);
            $user->unlikeUrl($this);
        }

        return $this;
    }
}
