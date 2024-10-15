<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PhotoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
#[ApiResource]
#[ORM\HasLifecycleCallbacks]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_photo = null;    

    #[ORM\ManyToOne(inversedBy: 'photos')]
    private ?User $add_by = null;

    /**
     * @var Collection<int, IncidentPhoto>
     */
    #[ORM\OneToMany(targetEntity: IncidentPhoto::class, mappedBy: 'photo')]
    private Collection $incidentPhotos;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $file = null;

    public function __construct()
    {
        $this->incidentPhotos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDatePhoto(): ?\DateTimeInterface
    {
        return $this->date_photo;
    }

    #[ORM\PrePersist]
    public function setDatePhoto(): void
    {
        $this->date_photo = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
    }

    public function getAddBy(): ?User
    {
        return $this->add_by;
    }

    public function setAddBy(?User $add_by): static
    {
        $this->add_by = $add_by;

        return $this;
    }

    /**
     * @return Collection<int, IncidentPhoto>
     */
    public function getIncidentPhotos(): Collection
    {
        return $this->incidentPhotos;
    }

    public function addIncidentPhoto(IncidentPhoto $incidentPhoto): static
    {
        if (!$this->incidentPhotos->contains($incidentPhoto)) {
            $this->incidentPhotos->add($incidentPhoto);
            $incidentPhoto->setPhoto($this);
        }

        return $this;
    }

    public function removeIncidentPhoto(IncidentPhoto $incidentPhoto): static
    {
        if ($this->incidentPhotos->removeElement($incidentPhoto)) {
            // set the owning side to null (unless already changed)
            if ($incidentPhoto->getPhoto() === $this) {
                $incidentPhoto->setPhoto(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getDescription();
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): static
    {
        $this->file = $file;

        return $this;
    }
}
