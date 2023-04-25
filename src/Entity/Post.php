<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Cocur\Slugify\Slugify;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("title")
 * @ApiResource(
 *   normalizationContext={"groups"={"read:Post:collection"}},
 *   collectionOperations={
 *       "get",
 *       "post"={
 *         "denormalization_context"={
 *           "groups"={"post:Post", "post:Category"}
 *         }
 *       },
 *   },
 *   itemOperations={
 *       "put"={
 *         "denormalization_context"={
 *           "groups"={"put:Post"}
 *         }
 *       },
 *       "delete",
 *       "get"={
 *          "normalization_context"={
 *             "groups"={"read:Post:item", "read:Category:item" }
 *           }
 *       }
 *   }
 * )
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:Post:collection", "read:Post:item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(min=5)
     * @Groups({"read:Post:collection", "read:Post:item", "put:Post", "post:Post" })
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"read:Post:collection"})
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=5)
     * @Groups({"read:Post:item", "put:Post", "post:Post" })
     */
    private $content;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="posts", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     * @Groups({ "read:Post:item", "put:Post", "post:Post" })
     */
    private $category;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->slug = "fill this value to be overrived later on";
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @Groups({ "put:Post", "post:Post" })
     */
    public function setSlugValue(): void
    {
        $slugify = new Slugify();
        $this->slug = $slugify->slugify($this->title);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
