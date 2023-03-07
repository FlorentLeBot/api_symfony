<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\CarBrand;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CarController extends AbstractController
{
    #[Route('/listeVoiture', name: 'listeVoiture', methods: ['GET'])]
    public function listCar(ManagerRegistry $doctrine): JsonResponse
    {
        $cars = $doctrine->getRepository(Car::class)->findAll();
        $data = [];

        foreach ($cars as $car) {
            $data[] = [
                'id' => $car->getId(),
                'numberPlate' => strtolower(trim($car->getNumberPlate())),
                'numberOfSeats' => $car->getNumberOfSeats(),
                'model' => strtolower(trim($car->getModel())),
                'brand' => strtolower(trim($car->getBrand()->getName()))
            ];
        }

        return $this->json($data);
    }

    //------------------------------------------------------------------------------------------------------------

    #[Route('/insertVoiture', name: 'insertVoiture', methods: ['POST'])]
    public function addCar(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();

            $numberPlate = $request->get('numberPlate');
            $numberOfSeats = $request->get('numberOfSeats');
            $model = $request->get('model');
            $brand = $request->get('brand');

            // On vérifie si les champs sont vides
            if (empty($numberPlate) || empty($numberOfSeats) || empty($model) || empty($brand)) {
                return $this->json([
                    'message' => 'Tous les champs sont obligatoires'
                ]);
            }

            // On vérifie si la plaque d'immatriculation existe déjà (on trime et on met en minuscule)
            if ($doctrine->getRepository(Car::class)->findOneBy(['numberPlate' => strtolower(trim($numberPlate))])) {
                return $this->json([
                    'message' => 'La voiture existe déjà'
                ]);
            }

            $car = new Car();

            $car->setNumberPlate(strtolower(trim($numberPlate)));

            // On vérifie si le nombre de sièges est un entier et il doit être supérieur à 0
            if (!is_int($numberOfSeats) && $numberOfSeats < 0) {

                return $this->json([
                    'message' => 'Le nombre de sièges doit être un entier supérieur à 0'
                ]);
            }
            $car->setNumberOfSeats($numberOfSeats);

            $car->setModel(strtolower(trim($model)));

            // On vérifie si la marque existe grâce à son id
            $carBrand = $doctrine->getRepository(CarBrand::class)->find($brand);
            if (!$carBrand) {
                return $this->json([
                    'message' => 'La marque n\'existe pas'
                ]);
            }

            $car->setBrand($carBrand);

            // On enregistre la voiture
            $entityManager->persist($car);
            $entityManager->flush();

            return $this->json([
                'message' => 'La voiture a bien été ajoutée'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    //------------------------------------------------------------------------------------------------------------

    #[Route('/updateVoiture/{id}', name: 'updateVoiture', methods: ['PUT'])]
    public function updateCar(Request $request, ManagerRegistry $doctrine, int $id): JsonResponse
    {
        try {

            // On récupère le manager de Doctrine
            $entityManager = $doctrine->getManager();

            // On récupère l'id de la voiture
            $car = $doctrine->getRepository(Car::class)->find($id);

            // On vérifie si la voiture existe
            if (!$car) {
                return $this->json([
                    'message' => 'La voiture n\'existe pas'
                ]);
            }

            $numberPlate = $request->get('numberPlate');
            $numberOfSeats = $request->get('numberOfSeats');
            $model = $request->get('model');
            $brand = $request->get('brand');

            // Si un des champs est vide, on ne modifie pas
            if (empty($numberPlate) || empty($numberOfSeats) || empty($model) || empty($brand)) {
                return $this->json([
                    'message' => 'Tous les champs sont obligatoires'
                ]);
            }

            // On met à jour la voiture
            $car->setNumberPlate(strtolower(trim($numberPlate)));

            // On vérifie si le nombre de sièges est un entier et il doit être supérieur à 0
            if (!is_int($numberOfSeats) && $numberOfSeats < 0) {

                return $this->json([
                    'message' => 'Le nombre de sièges doit être un entier supérieur à 0'
                ]);
            }
            $car->setNumberOfSeats($numberOfSeats);

            $car->setModel(strtolower(trim($model)));

            // On vérifie si la marque existe grâce à son id
            $carBrand = $doctrine->getRepository(CarBrand::class)->find($brand);
            if (!$carBrand) {
                return $this->json([
                    'message' => 'La marque n\'existe pas'
                ]);
            }

            // On enregistre la voiture modifiée
            $entityManager->persist($car);
            $entityManager->flush();

            return $this->json([
                'message' => 'La voiture a bien été modifiée'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    //------------------------------------------------------------------------------------------------------------

    #[Route('/deleteVoiture/{id}', name: 'deleteVoiture', methods: ['DELETE'])]
    public function deleteCar(Request $request, ManagerRegistry $doctrine, int $id): JsonResponse
    {
        try {
            // On récupère le manager de Doctrine
            $entityManager = $doctrine->getManager();

            // On récupère l'id de la voiture
            $id = $request->get('id');

            // On vérifie si la voiture existe
            if (!$doctrine->getRepository(Car::class)->findOneBy(['id' => $id])) {
                return $this->json([
                    'message' => 'La voiture n\'existe pas'
                ]);
            }

            // On récupère la marque de voiture
            $car = $doctrine->getRepository(Car::class)->findOneBy(['id' => $id]);

            // On supprime la marque de voiture
            $entityManager->remove($car);
            $entityManager->flush();

            return $this->json([
                'message' => 'La marque a bien été supprimée'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage()
            ]);
        }
    }
}
