<?php
namespace App\Filter;

use App\Entity\User;

class BlogFilter {

    public function __construct(private readonly ?User $user = null)
    {
    }

    public function getUser(): ?User
    {
        return $this->user;
    }


    private ?string $title = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }
}