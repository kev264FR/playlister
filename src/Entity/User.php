<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $username;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="followers")
     */
    private $followedUsers;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="followedUsers")
     */
    private $followers;

    /**
     * @ORM\OneToMany(targetEntity=Playlist::class, mappedBy="user")
     */
    private $myPlaylists;

    /**
     * @ORM\ManyToMany(targetEntity=Playlist::class, inversedBy="followers")
     * @ORM\JoinTable(name="user_followed_playlists")
     */
    private $followedPlaylists;

    /**
     * @ORM\ManyToMany(targetEntity=Playlist::class, inversedBy="likers")
     * @ORM\JoinTable(name="user_liked_playlists")
     */
    private $likedPlaylists;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="user")
     */
    private $comments;



    public function __construct()
    {
        $this->followedUsers = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->myPlaylists = new ArrayCollection();
        $this->followedPlaylists = new ArrayCollection();
        $this->likedPlaylists = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

 

    /**
     * @return Collection|Playlist[]
     */
    public function getMyPlaylists(): Collection
    {
        return $this->myPlaylists;
    }

    public function addMyPlaylist(Playlist $myPlaylist): self
    {
        if (!$this->myPlaylists->contains($myPlaylist)) {
            $this->myPlaylists[] = $myPlaylist;
            $myPlaylist->setUser($this);
        }

        return $this;
    }

    public function removeMyPlaylist(Playlist $myPlaylist): self
    {
        if ($this->myPlaylists->removeElement($myPlaylist)) {
            // set the owning side to null (unless already changed)
            if ($myPlaylist->getUser() === $this) {
                $myPlaylist->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Playlist[]
     */
    public function getFollowedPlaylists(): Collection
    {
        return $this->followedPlaylists;
    }

    public function addFollowedPlaylist(Playlist $followedPlaylist): self
    {
        if (!$this->followedPlaylists->contains($followedPlaylist)) {
            $this->followedPlaylists[] = $followedPlaylist;
        }

        return $this;
    }

    public function removeFollowedPlaylist(Playlist $followedPlaylist): self
    {
        $this->followedPlaylists->removeElement($followedPlaylist);

        return $this;
    }

    /**
     * @return Collection|Playlist[]
     */
    public function getLikedPlaylists(): Collection
    {
        return $this->likedPlaylists;
    }

    public function addLikedPlaylist(Playlist $likedPlaylist): self
    {
        if (!$this->likedPlaylists->contains($likedPlaylist)) {
            $this->likedPlaylists[] = $likedPlaylist;
        }

        return $this;
    }

    public function removeLikedPlaylist(Playlist $likedPlaylist): self
    {
        $this->likedPlaylists->removeElement($likedPlaylist);

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getFollowedUsers(): Collection
    {
        return $this->followedUsers;
    }

    public function addFollowedUser(User $followedUser): self
    {
        if (!$this->followedUsers->contains($followedUser)) {
            $this->followedUsers[] = $followedUser;
            $followedUser->addFollower($this);
        }

        return $this;
    }

    public function removeFollowedUser(User $followedUser): self
    {
        if ($this->followedUsers->removeElement($followedUser)) {
            $followedUser->removeFollower($this);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(User $follower): self
    {
        if (!$this->followers->contains($follower)) {
            $this->followers[] = $follower;
        }

        return $this;
    }

    public function removeFollower(User $follower): self
    {
        $this->followers->removeElement($follower);

        return $this;
    }

}
