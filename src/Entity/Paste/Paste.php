<?php

namespace App\Entity\Paste;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Api\Paste\PasteController;
use App\Controller\Api\Paste\PasteCreateController;
use App\Entity\User;
use App\Repository\Paste\PasteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @ORM\Entity(repositoryClass=PasteRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        "public" => [
            'method' => "get",
            'path' => '/pastes/public',
            'openapi_context' => ['summary' => 'Retrieves only public paste'],
            'controller' => PasteController::class,
        ],
        "post" => [
            "controller" => PasteCreateController::class,
            "denormalization_context" => ["groups" => ["write:paste", "create:paste"]],
        ]
    ],
    itemOperations: [
        "get" => [
            "security" => "is_granted('CAN_READ_PASTE', object)"
        ],
        "put" => [
            "security" => "is_granted('CAN_EDIT_PASTE', object)",
        ],
        "delete" => [
            "security" => "is_granted('CAN_DELETE_PASTE', object)",
        ]
    ],
    attributes: [
        'order' => ['createdAt' => 'DESC']
    ],
    denormalizationContext: ["groups" => ["write:paste"]],
    normalizationContext: ["groups" => ["read:paste"]],
    paginationItemsPerPage: 10
)]
class Paste
{

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->reports = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:paste', 'user:read:paste'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups(['read:paste', 'write:paste', 'user:read:paste'])]
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    #[Groups(['read:paste', 'write:paste', 'user:read:paste'])]
    #[NotBlank()]
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:paste', 'write:paste', 'user:read:paste'])]
    private $privacy = "unlisted";

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    #[Groups(['read:paste', 'user:read:paste'])]
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:paste', 'write:paste', 'user:read:paste'])]
    #[NotBlank()]
    private $language = "text";

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="pastes")
     */
    #[Groups(['read:paste', 'create:paste'])]
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Report::class, mappedBy="paste", orphanRemoval=true)
     */
    private $reports;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPrivacy(): ?string
    {
        return $this->privacy;
    }

    public function setPrivacy(string $privacy): self
    {
        $this->privacy = $privacy;

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

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

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
            $report->setPaste($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getPaste() === $this) {
                $report->setPaste(null);
            }
        }

        return $this;
    }
}
