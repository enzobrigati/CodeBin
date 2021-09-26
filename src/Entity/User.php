<?php

namespace App\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Api\Paste\UserGetPastesController;
use App\Controller\Api\User\MeController;
use App\Entity\Paste\Paste;
use App\Entity\Paste\Report;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        'me' => [
            'pagination_enabled' => false,
            'path' => '/me',
            'method' => 'get',
            'controller' => MeController::class,
            'security' => 'is_granted("ROLE_USER")'
        ],
        "userpastes" => [
            'method' => 'get',
            'path' => '/user/pastes',
            'controller' => UserGetPastesController::class,
            'openapi_context' => ['summary' => 'Retrieves pastes for the logged user'],
            "security" => "is_granted('ROLE_USER')",
            "normalization_context" => ['groups' => ['user:read:paste']]
        ],
    ],
    itemOperations: [
        "get" => [
            "denormalization_context" => ['groups' => ['read:paste']]
        ]
    ],
    normalizationContext: ['groups' => ['read:user']]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->pastes = new ArrayCollection();
        $this->reports = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:paste', 'read:user'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    #[NotBlank()]
    #[Groups(['read:user'])]
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    #[Groups(['read:user'])]
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    #[NotBlank()]
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[NotBlank()]
    #[Groups(['read:paste', 'read:user'])]
    private $pseudo;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Paste::class, mappedBy="user")
     */
    #[Groups(['user:read:paste'])]
    private $pastes;

    /**
     * @ORM\OneToMany(targetEntity=Report::class, mappedBy="owner", orphanRemoval=true)
     */
    private $reports;

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
    public function getUserIdentifier(): string
    {
        return (string)$this->pseudo;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string)$this->pseudo;
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
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
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

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|Paste[]
     */
    public function getPastes(): Collection
    {
        return $this->pastes;
    }

    public function addPaste(Paste $paste): self
    {
        if (!$this->pastes->contains($paste)) {
            $this->pastes[] = $paste;
            $paste->setUser($this);
        }

        return $this;
    }

    public function removePaste(Paste $paste): self
    {
        if ($this->pastes->removeElement($paste)) {
            // set the owning side to null (unless already changed)
            if ($paste->getUser() === $this) {
                $paste->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Report[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setOwner($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getOwner() === $this) {
                $report->setOwner(null);
            }
        }

        return $this;
    }
}
