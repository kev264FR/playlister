<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Playlist::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $playlist;

    /**
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="answers")
     */
    private $answerFor;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="answerFor")
     */
    private $answers;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;


    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPlaylist(): ?Playlist
    {
        return $this->playlist;
    }

    public function setPlaylist(?Playlist $playlist): self
    {
        $this->playlist = $playlist;

        return $this;
    }

    public function getAnswerFor(): ?self
    {
        return $this->answerFor;
    }

    public function setAnswerFor(?self $AnswerFor): self
    {
        $this->answerFor = $AnswerFor;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Comment $Answer): self
    {
        if (!$this->answers->contains($Answer)) {
            $this->answers[] = $Answer;
            $Answer->setAnswerFor($this);
        }

        return $this;
    }

    public function removeAnswer(Comment $Answer): self
    {
        if ($this->answers->removeElement($Answer)) {
            // set the owning side to null (unless already changed)
            if ($Answer->getAnswerFor() === $this) {
                $Answer->setAnswerFor(null);
            }
        }

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

    
}
