<?php

namespace App\Entity;

use App\Repository\AnimeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimeRepository::class)]
class Anime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $numberOfEpisodes = null;

    #[ORM\ManyToOne(inversedBy: 'animes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $entryAuthor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getNumberOfEpisodes(): ?int
    {
        return $this->numberOfEpisodes;
    }

    public function setNumberOfEpisodes(int $numberOfEpisodes): static
    {
        $this->numberOfEpisodes = $numberOfEpisodes;

        return $this;
    }

    public function getEntryAuthor(): ?User
    {
        return $this->entryAuthor;
    }

    public function setEntryAuthor(?User $entryAuthor): static
    {
        $this->entryAuthor = $entryAuthor;

        return $this;
    }
}
