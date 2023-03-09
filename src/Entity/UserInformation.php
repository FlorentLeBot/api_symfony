<?php

namespace App\Entity;

use App\Repository\UserInformationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserInformationRepository::class)]
class UserInformation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $firstname = null;

    #[ORM\Column(length: 100)]
    private ?string $lastname = null;

    #[ORM\Column(length: 20)]
    private ?string $phone = null;

    #[ORM\Column(length: 100)]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    private ?string $city = null;

    #[ORM\ManyToMany(targetEntity: Car::class, inversedBy: 'id_UserInformation', cascade: ['persist', 'remove'])]
    private Collection $id_Car;

    #[ORM\ManyToMany(targetEntity: Travel::class, inversedBy: 'id_userInformation', cascade: ['persist', 'remove'])]
    private Collection $id_travel;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;


    public function __construct()
    {
        $this->id_Car = new ArrayCollection();
        $this->id_travel = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection<int, Car>
     */
    public function getIdCar(): Collection
    {
        return $this->id_Car;
    }

    public function addIdCar(Car $idCar): self
    {
        if (!$this->id_Car->contains($idCar)) {
            $this->id_Car->add($idCar);
        }

        return $this;
    }

    public function removeIdCar(Car $idCar): self
    {
        $this->id_Car->removeElement($idCar);

        return $this;
    }

    /**
     * @return Collection<int, Travel>
     */
    public function getIdTravel(): Collection
    {
        return $this->id_travel;
    }

    public function addIdTravel(Travel $idTravel): self
    {
        if (!$this->id_travel->contains($idTravel)) {
            $this->id_travel->add($idTravel);
        }

        return $this;
    }

    public function removeIdTravel(Travel $idTravel): self
    {
        $this->id_travel->removeElement($idTravel);

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
    
    public function getVehicle(): ?string
    {
        $vehicle = '';
        foreach ($this->id_Car as $car) {
            $vehicle .= $car->getNumberPlate() . ' ';
        }
        return $vehicle;
    } 
}
