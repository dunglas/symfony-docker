<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\IncidentPhotoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IncidentPhotoRepository::class)]
#[ApiResource]
class IncidentPhoto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'incidentPhotos')]
    private ?incident $incident = null;

    #[ORM\ManyToOne(inversedBy: 'incidentPhotos')]
    private ?photo $photo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIncident(): ?incident
    {
        return $this->incident;
    }

    public function setIncident(?incident $incident): static
    {
        $this->incident = $incident;

        return $this;
    }

    public function getPhoto(): ?photo
    {
        return $this->photo;
    }

    public function setPhoto(?photo $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

}
