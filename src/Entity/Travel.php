<?php

namespace App\Entity;

use App\Repository\TravelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\ManyToMany(targetEntity: UserInformation::class, mappedBy: 'id_travel')]
    private Collection $id_userInformation;

    #[ORM\ManyToOne(inversedBy: 'travel')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserInformation $driver = null;

    public function __construct()
    {
        $this->id_userInformation = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, UserInformation>
     */
    public function getIdUserInformation(): Collection
    {
        return $this->id_userInformation;
    }

    public function addIdUserInformation(UserInformation $idUserInformation): self
    {
        if (!$this->id_userInformation->contains($idUserInformation)) {
            $this->id_userInformation->add($idUserInformation);
            $idUserInformation->addIdTravel($this);
        }

        return $this;
    }

    public function removeIdUserInformation(UserInformation $idUserInformation): self
    {
        if ($this->id_userInformation->removeElement($idUserInformation)) {
            $idUserInformation->removeIdTravel($this);
        }

        return $this;
    }

    public function getDriver(): ?UserInformation
    {
        return $this->driver;
    }

    public function setDriver(?UserInformation $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

}
