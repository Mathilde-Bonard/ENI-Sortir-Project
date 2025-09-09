<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

class SortieFilter
{
    private ?Campus $campus = null;

    private ?string $nom = null;

    #[Assert\GreaterThan('today')]
    private ?\DateTimeInterface $dateIntervalDebut = null;

    #[Assert\GreaterThan('dateIntervalDebut')]
    private ?\DateTimeInterface $dateIntervalFin = null;

    private array $filters = [];

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): void
    {
        $this->campus = $campus;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function getDateIntervalDebut(): ?\DateTimeInterface
    {
        return $this->dateIntervalDebut;
    }

    public function setDateIntervalDebut(?\DateTimeInterface $dateIntervalDebut): void
    {
        $this->dateIntervalDebut = $dateIntervalDebut;
    }

    public function getDateIntervalFin(): ?\DateTimeInterface
    {
        return $this->dateIntervalFin;
    }

    public function setDateIntervalFin(?\DateTimeInterface $dateIntervalFin): void
    {
        $this->dateIntervalFin = $dateIntervalFin;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }
}
