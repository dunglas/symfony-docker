<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\IncidentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IncidentRepository::class)]
#[ApiResource]
class Incident
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_incident = null;

    #[ORM\ManyToOne(inversedBy: 'incidents')]
    private ?bus $bus = null;

    #[ORM\ManyToOne(inversedBy: 'incidents')]
    private ?line $line = null;

    #[ORM\ManyToOne(inversedBy: 'incidents')]
    private ?user $user = null;

    /**
     * @var Collection<int, IncidentPhoto>
     */
    #[ORM\OneToMany(targetEntity: IncidentPhoto::class, mappedBy: 'incident_id')]
    private Collection $incidentPhotos;

    public function __construct()
    {
        $this->incidentPhotos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateIncident(): ?\DateTimeInterface
    {
        return $this->date_incident;
    }

    public function setDateIncident(\DateTimeInterface $date_incident): static
    {
        $this->date_incident = $date_incident;

        return $this;
    }

    public function getBus(): ?bus
    {
        return $this->bus;
    }

    public function setBus(?bus $bus): static
    {
        $this->bus = $bus;

        return $this;
    }

    public function getLine(): ?line
    {
        return $this->line;
    }

    public function setLine(?line $line): static
    {
        $this->line = $line;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

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
            $incidentPhoto->setIncident($this);
        }

        return $this;
    }

    public function removeIncidentPhoto(IncidentPhoto $incidentPhoto): static
    {
        if ($this->incidentPhotos->removeElement($incidentPhoto)) {
            // set the owning side to null (unless already changed)
            if ($incidentPhoto->getIncident() === $this) {
                $incidentPhoto->setIncident(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getId();
    }
}
