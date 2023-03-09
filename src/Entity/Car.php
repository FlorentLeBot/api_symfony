<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numberPlate = null;

    #[ORM\Column]
    private ?int $numberOfSeats = null;

    #[ORM\Column(length: 100)]
    private ?string $model = null;

    #[ORM\ManyToOne(targetEntity: CarBrand::class, inversedBy: 'id_Car', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?CarBrand $brand = null;

    #[ORM\ManyToMany(targetEntity: UserInformation::class, mappedBy: 'id_Car' , cascade: ['persist', 'remove'])]
    private Collection $id_UserInformation;

    public function __construct()
    {
        $this->id_UserInformation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumberPlate(): ?string
    {
        return $this->numberPlate;
    }

    public function setNumberPlate(string $numberPlate): self
    {
        $this->numberPlate = $numberPlate;

        return $this;
    }

    public function getNumberOfSeats(): ?int
    {
        return $this->numberOfSeats;
    }

    public function setNumberOfSeats(int $numberOfSeats): self
    {
        $this->numberOfSeats = $numberOfSeats;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getBrand(): ?CarBrand
    {
        return $this->brand;
    }

    public function setBrand(?CarBrand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return Collection<int, UserInformation>
     */
    public function getIdUserInformation(): Collection
    {
        return $this->id_UserInformation;
    }

    public function addIdUserInformation(UserInformation $idUserInformation): self
    {
        if (!$this->id_UserInformation->contains($idUserInformation)) {
            $this->id_UserInformation->add($idUserInformation);
            $idUserInformation->addIdCar($this);
        }
        return $this;
    }

    public function removeIdUserInformation(UserInformation $idUserInformation): self
    {
        if ($this->id_UserInformation->removeElement($idUserInformation)) {
            $idUserInformation->removeIdCar($this);
        }

        return $this;
    }

   

}
