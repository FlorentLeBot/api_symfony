<?php

namespace App\Controller;

use App\Entity\UserInformation;

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
            ];
        }

        return $this->json($data);
    }

    //------------------------------------------------------------------------------------------------------------

    // ajouter une personne

    #[Route('/insertPersonne', name: 'insertPersonne')]
    public function addUserInformation(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();

            $firstName = $request->get('firstName');
            $lastName = $request->get('lastName');
            $email = $request->get('email');
            $phone = $request->get('phone');
            $city = $request->get('city');

            // On vérifie si les champs sont vides

            if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($city)) {
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

    // modifier une personne

    #[Route('/updatePersonne/{id}', name: 'updatePersonne')]
    public function updateUserInformation(Request $request, ManagerRegistry $doctrine, int $id): JsonResponse
    {
        
        try {
            $entityManager = $doctrine->getManager();

            $firstName = $request->get('firstName');
            $lastName = $request->get('lastName');
            $email = $request->get('email');
            $phone = $request->get('phone');
            $city = $request->get('city');

            // On vérifie si les champs sont vides

            if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($city)) {
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
            
            // On récupère l'entité UserInformation
            $userInformation = $doctrine->getRepository(UserInformation::class)->find($id);

            // On hydrate l'entité UserInformation
            $userInformation->setFirstname($firstName);
            $userInformation->setLastname($lastName);
            $userInformation->setEmail($email);
            $userInformation->setPhone($phone);
            $userInformation->setCity($city);

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

    // afficher une personne

    #[Route('/selectPersonne/{id}', name: 'selectPersonne')]
    public function selectUserInformation(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();

            // On récupère l'entité UserInformation
            $userInformation = $doctrine->getRepository(UserInformation::class)->find($id);

            $data = [
                'id' => $userInformation->getId(),
                'firstName' => $userInformation->getFirstName(),
                'lastName' => $userInformation->getLastName(),
                'email' => $userInformation->getEmail(),
                'phone' => $userInformation->getPhone(),
                'city' => $userInformation->getCity(),  
            ];

            return $this->json($data);

        } catch (\Exception $e) {

            return $this->json([
                'message' => $e->getMessage()
            ]);
        }

    }
}