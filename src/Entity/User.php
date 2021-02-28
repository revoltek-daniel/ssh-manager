<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    private string $email;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $groups;

    /**
     * @ORM\ManyToMany(targetEntity=Server::class, mappedBy="users")
     * @ORM\JoinTable(name="server_user")
     */
    private $servers;

    /**
     * @Assert\Length(min=8, max=128)
     */
    private ?string $plainPassword = null;

    /**
     * @ORM\OneToMany(targetEntity=SshKey::class, mappedBy="user", cascade="persist")
     */
    private $sshKeys;

    public function __construct()
    {
        $this->servers = new ArrayCollection();
        $this->sshKeys = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
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
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getGroups(): ?Group
    {
        return $this->groups;
    }

    public function setGroups(?Group $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * @return Collection|Server[]
     */
    public function getServers(): Collection
    {
        return $this->servers;
    }

    public function addServer(Server $server): self
    {
        if (!$this->servers->contains($server)) {
            $server->addUser($this);
            $this->servers[] = $server;
        }

        return $this;
    }

    public function removeServer(Server $server): self
    {
        if ($this->servers->removeElement($server)) {
            $server->removeUser($this);
        }

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $password): void
    {
        $this->plainPassword = $password;
    }

    /**
     * @return Collection|SshKey[]
     */
    public function getSshKeys(): Collection
    {
        return $this->sshKeys;
    }

    public function addSshKey(SshKey $sshKey): self
    {
        if (!$this->sshKeys->contains($sshKey)) {
            $this->sshKeys[] = $sshKey;
            $sshKey->setUser($this);
        }

        return $this;
    }

    public function removeSshKey(SshKey $sshKey): self
    {
        if ($this->sshKeys->removeElement($sshKey)) {
            // set the owning side to null (unless already changed)
            if ($sshKey->getUser() === $this) {
                $sshKey->setUser(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getUsername();
    }
}
