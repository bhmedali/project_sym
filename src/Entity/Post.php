<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity()]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(targetEntity: Forum::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Forum $forum = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, cascade: ['persist', 'remove'])]
    private Collection $comments;

    #[ORM\Column(type: 'text')]  // Ensure content is defined as a text column
    private ?string $content = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getForum(): ?Forum
    {
        return $this->forum;
    }

    public function setForum(?Forum $forum): static
    {
        $this->forum = $forum;

        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this); // set inverse side
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
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

    // Getter for content
    public function getContent(): ?string
    {
        return $this->content;
    }

    // Setter for content
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

}


