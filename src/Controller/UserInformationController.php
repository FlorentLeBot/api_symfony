<?php

namespace App\Controller;

use App\Entity\UserInformation;
use App\Entity\User;
use App\Entity\Car;
use App\Entity\CarBrand;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserInformationController extends AbstractController
{
    #[Route('/listePersonne', name: 'listePersonne')]
    public function listUserInformation(ManagerRegistry $doctrine): JsonResponse
    {
        // liste des personnes de l'entité UserInformation
        $userInformations = $doctrine->getRepository(UserInformation::class)->findAll();

        $data = [];

        foreach ($userInformations as $userInformation) {
            $data[] = [
                'id' => $userInformation->getId(),
                'firstName' => $userInformation->getFirstName(),
                'lastName' => $userInformation->getLastName(),
                'email' => $userInformation->getEmail(),
                'phone' => $userInformation->getPhone(),
                'city' => $userInformation->getCity(),
                // On récupére les informations de l'entité Car
                'car' => $userInformation->getIdCar()->map(function ($car) {
                    return [
                        'numberPlate' => $car->getNumberPlate(),
                        'numberOfSeats' => $car->getNumberOfSeats(),
                        'model' => $car->getModel(),
                        'brand' => $car->getBrand()->getName(),
                    ];
                })->toArray(),
                // On récupére les informations de l'entité User
                'user' => [
                    'id' => $userInformation->getUser()->getId(),
                    'username' => $userInformation->getUser()->getUsername(),
                    'email' => $userInformation->getUser()->getEmail(),
                ]
            ];
        }

        return $this->json($data);
    }

    //------------------------------------------------------------------------------------------------------------

    // ajouter une personne

    #[Route('/insertPersonne', name: 'insertPersonne', methods: ['POST'])]
    public function addUserInformation(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();

            // On récupére l'entité User grâce à l'id
            $user = $doctrine->getRepository(User::class)->find($request->get('user'));

            $firstName = $request->get('firstName');
            $lastName = $request->get('lastName');
            $email = $request->get('email');
            $phone = $request->get('phone');
            $city = $request->get('city');
            $brand = $request->get('brand');

            // On vérifie si les champs sont vides
            if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($city) || empty($user) || empty($brand)) {
                return $this->json([
                    'message' => 'Veuillez remplir tous les champs',
                ]);
            }

            // On vérifie si l'email est valide
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->json([
                    'message' => 'Veuillez entrer un email valide',
                ]);
            }

            // On vérifie si le numéro de téléphone est valide
            if (!preg_match('/^[0-9]{10}$/', $phone)) {
                return $this->json([
                    'message' => 'Veuillez entrer un numéro de téléphone valide, exemple : 0601020304',
                ]);
            }

            // On vérifie si l'email existe déjà
            $emailExist = $doctrine->getRepository(UserInformation::class)->findOneBy(['email' => $email]);
            if ($emailExist) {
                return $this->json([
                    'message' => 'Cet email existe déjà',
                ]);
            }

            // On vérifie si le numéro de téléphone existe déjà
            $phoneExist = $doctrine->getRepository(UserInformation::class)->findOneBy(['phone' => $phone]);

            if ($phoneExist) {
                return $this->json([
                    'message' => 'Ce numéro de téléphone existe déjà',
                ]);
            }

            // On crée l'entité UserInformation
            $userInformation = new UserInformation();

            // On hydrate l'entité UserInformation
            $userInformation->setFirstname($firstName);
            $userInformation->setLastname($lastName);
            $userInformation->setEmail($email);
            $userInformation->setPhone($phone);
            $userInformation->setCity($city);

            // On crée l'entité Car
            $car = new Car();

            // On hydrate l'entité Car
            $car->setNumberPlate($request->get('numberPlate'));
            $car->setNumberOfSeats($request->get('numberOfSeats'));
            $car->setModel($request->get('model'));

            // On crée l'entité CarBrand
            $carBrand = new CarBrand();

            // On hydrate l'entité CarBrand et on l'ajoute à l'entité Car 
            $carBrand->setName($request->get('brand'));
            $car->setBrand($carBrand);


            // On ajoute l'entité User à l'entité UserInformation
            $userInformation->setUser($user);

            // On ajoute l'entité Car à l'entité UserInformation
            $car->addIdUserInformation($userInformation);

            // On enregistre l'entité UserInformation
            $entityManager->persist($userInformation);
            $entityManager->flush();

            return $this->json([
                'message' => 'La personne a bien été ajoutée',
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    //------------------------------------------------------------------------------------------------------------

    // modifier une personne grâce à son id : on récupère les informations de la personne, de la voiture et la marque de la voiture pour pouvoir les modifier

    #[Route('/updatePersonne/{id}', name: 'updatePersonne')]
    public function updateUserInformation(Request $request, ManagerRegistry $doctrine, int $id): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();

            // On récupére l'entité UserInformation grâce à l'id
            $userInformation = $doctrine->getRepository(UserInformation::class)->find($id);

            // On récupére l'entité Car grâce à l'id
            $car = $doctrine->getRepository(Car::class)->find($id);

            // On récupére l'entité CarBrand grâce à l'id
            $carBrand = $doctrine->getRepository(CarBrand::class)->find($id);

            // On vérifie si les champs sont vides
            if (empty($request->get('firstName')) || empty($request->get('lastName')) || empty($request->get('email')) || empty($request->get('phone')) || empty($request->get('city'))) {
                return $this->json([
                    'message' => 'Veuillez remplir tous les champs',
                ]);
            }

            // On vérifie si l'email est valide
            if (!filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
                return $this->json([
                    'message' => 'Veuillez entrer un email valide',
                ]);
            }

            // On vérifie si le numéro de téléphone est valide
            if (!preg_match('/^[0-9]{10}$/', $request->get('phone'))) {
                return $this->json([
                    'message' => 'Veuillez entrer un numéro de téléphone valide, exemple : 0601020304',
                ]);
            }

            // On vérifie si l'email existe déjà
            $emailExist = $doctrine->getRepository(UserInformation::class)->findOneBy(['email' => $request->get('email')]);
            if ($emailExist) {
                return $this->json([
                    'message' => 'Cet email existe déjà',
                ]);
            }

            // On vérifie si le numéro de téléphone existe déjà
            $phoneExist = $doctrine->getRepository(UserInformation::class)->findOneBy(['phone' => $request->get('phone')]);
            if ($phoneExist) {
                return $this->json([
                    'message' => 'Ce numéro de téléphone existe déjà',
                ]);
            }

            // On hydrate l'entité UserInformation
            $userInformation->setFirstname($request->get('firstName'));
            $userInformation->setLastname($request->get('lastName'));
            $userInformation->setEmail($request->get('email'));
            $userInformation->setPhone($request->get('phone'));
            $userInformation->setCity($request->get('city'));

            // On hydrate l'entité Car
            $car->setNumberPlate($request->get('numberPlate'));
            $car->setNumberOfSeats($request->get('numberOfSeats'));
            $car->setModel($request->get('model'));

            // On hydrate l'entité CarBrand
            $carBrand->setName($request->get('brand'));

            // On enregistre l'entité UserInformation
            $entityManager->persist($userInformation);
            $entityManager->flush();

            return $this->json([
                'message' => 'La personne a bien été modifiée',
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    //------------------------------------------------------------------------------------------------------------

    // supprimer une personne

    #[Route('/deletePersonne/{id}', name: 'deletePersonne')]
    public function deleteUserInformation(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();

            // On récupère l'entité UserInformation
            $userInformation = $doctrine->getRepository(UserInformation::class)->find($id);

            // On supprime l'entité UserInformation
            $entityManager->remove($userInformation);
            $entityManager->flush();

            return $this->json([
                'message' => 'La personne a bien été supprimée',
            ]);
        } catch (\Exception $e) {

            return $this->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    //------------------------------------------------------------------------------------------------------------

    // afficher une personne en fonction de son id

    #[Route('/selectPersonne/{id}', name: 'selectPersonne')]
    public function selectUserInformation(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        try {
            // On récupère l'entité UserInformation
            $userInformation = $doctrine->getRepository(UserInformation::class)->find($id);

            // On hydrate le tableau $data
            $data = [
                'id' => $userInformation->getId(),
                'firstName' => $userInformation->getFirstName(),
                'lastName' => $userInformation->getLastName(),
                'email' => $userInformation->getEmail(),
                'phone' => $userInformation->getPhone(),
                'city' => $userInformation->getCity(),
                // On récupére les informations de l'entité Car
                'car' => $userInformation->getIdCar()->map(function ($car) {
                    return [
                        'numberPlate' => $car->getNumberPlate(),
                        'numberOfSeats' => $car->getNumberOfSeats(),
                        'model' => $car->getModel(),
                        'brand' => $car->getBrand()->getName(),
                    ];
                })->toArray(),
                // On récupére les informations de l'entité User
                'user' => [
                    'id' => $userInformation->getUser()->getId(),
                    'username' => $userInformation->getUser()->getUsername(),
                    'email' => $userInformation->getUser()->getEmail(),
                ]
            ];

            return $this->json($data);
        } catch (\Exception $e) {

            return $this->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    //------------------------------------------------------------------------------------------------------------           
}
