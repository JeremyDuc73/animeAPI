<?php

namespace App\Entity;

use App\Repository\StudioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: StudioRepository::class)]
class Studio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['anime', 'studio'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['anime', 'studio'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Anime>
     */
    #[ORM\OneToMany(targetEntity: Anime::class, mappedBy: 'studio')]
    private Collection $animes;

    #[ORM\ManyToOne(inversedBy: 'studios')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['studio'])]
    private ?User $entryAuthor = null;

    public function __construct()
    {
        $this->animes = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Anime>
     */
    public function getAnimes(): Collection
    {
        return $this->animes;
    }

    public function addAnime(Anime $anime): static
    {
        if (!$this->animes->contains($anime)) {
            $this->animes->add($anime);
            $anime->setStudio($this);
        }

        return $this;
    }

    public function removeAnime(Anime $anime): static
    {
        if ($this->animes->removeElement($anime)) {
            // set the owning side to null (unless already changed)
            if ($anime->getStudio() === $this) {
                $anime->setStudio(null);
            }
        }

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
