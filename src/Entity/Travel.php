<?php

namespace App\Entity;

use App\Repository\TravelRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TravelRepository::class)]
class Travel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DateOfDeparture = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $arrivalDate = null;

    #[ORM\Column]
    private ?int $kilometer = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $startingCity = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $arrivalCity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateOfDeparture(): ?\DateTimeInterface
    {
        return $this->DateOfDeparture;
    }

    public function setDateOfDeparture(\DateTimeInterface $DateOfDeparture): self
    {
        $this->DateOfDeparture = $DateOfDeparture;

        return $this;
    }

    public function getArrivalDate(): ?\DateTimeInterface
    {
        return $this->arrivalDate;
    }

    public function setArrivalDate(\DateTimeInterface $arrivalDate): self
    {
        $this->arrivalDate = $arrivalDate;

        return $this;
    }

    public function getKilometer(): ?int
    {
        return $this->kilometer;
    }

    public function setKilometer(int $kilometer): self
    {
        $this->kilometer = $kilometer;

        return $this;
    }

    public function getStartingCity(): ?City
    {
        return $this->startingCity;
    }

    public function setStartingCity(?City $startingCity): self
    {
        $this->startingCity = $startingCity;

        return $this;
    }

    public function getArrivalCity(): ?City
    {
        return $this->arrivalCity;
    }

    public function setArrivalCity(?City $arrivalCity): self
    {
        $this->arrivalCity = $arrivalCity;

        return $this;
    }
}
