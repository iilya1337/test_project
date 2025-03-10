<?php

namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: BlogRepository::class)]
class Blog
{

    public function __construct(UserInterface|User $user)
    {
        $this->user = $user;
    }

    #[ORM\Id,
        ORM\GeneratedValue,
        ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Category::class),
        ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id')]
    private ?Category $category = null;

    #[ORM\ManyToOne(targetEntity: User::class),
        ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    #[ORM\JoinTable(name: 'tags_to_blog'),
        ORM\JoinColumn(name: 'blog_id', referencedColumnName: 'id'),
        ORM\InverseJoinColumn(name: 'tag_id', referencedColumnName: 'id'),
        ORM\ManyToMany(targetEntity: Tag::class)]
    private ArrayCollection|PersistentCollection|null $tags = null;

    public function getTags(): ?Collection
    {
        return $this->tags;
    }

    public function setTags(ArrayCollection $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function addTag(Tag $tag): void
    {
        $this->tags[] = $tag;
    }

    public function getTagNamesString(): string
    {
        return implode(', ', $this->tags->map(fn($tag) => $tag->getName())->toArray());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
    public function getUserToString(): ?string
    {
        return $this->user->getEmail();
    }
    public function getTagsToString(): ?string
    {
        return implode(',', array_map(fn($tag) => $tag->getName(), $this->tags->toArray()));
    }

}