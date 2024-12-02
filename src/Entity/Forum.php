<?php

// src/Entity/Forum.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class Forum
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'forum', targetEntity: Post::class, cascade: ['persist', 'remove'])]
    private Collection $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    // Getter for id
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter for title
    public function getTitle(): ?string
    {
        return $this->title;
    }

    // Setter for title
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    // Getter for description
    public function getDescription(): ?string
    {
        return $this->description;
    }

    // Setter for description
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    // Methods for posts
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setForum($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            if ($post->getForum() === $this) {
                $post->setForum(null);
            }
        }

        return $this;
    }
}
