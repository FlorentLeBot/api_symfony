<?php

namespace App\Controller;

use App\Entity\City;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends AbstractController
{
    // liste des codes postaux

    #[Route('/listeCodePostal', name: 'listeCodePostal')]
    public function listPostalCode(ManagerRegistry $doctrine): JsonResponse
    {
        $postalCode = $doctrine->getRepository(City::class)->findAll();
        $data = [];

        foreach ($postalCode as $code) {
            $data[] = [
                'postalCode' => strtolower(trim($code->getPostalCode()))
            ];
        }

        return $this->json($data);
    }

    //------------------------------------------------------------------------------------------------------------



    #[Route('/listeVille', name: 'listeVille')]
    public function listCity(ManagerRegistry $doctrine): JsonResponse
    {
        $cities = $doctrine->getRepository(City::class)->findBy([], ['name' => 'ASC']);
        $data = [];

        foreach ($cities as $city) {
            $data[] = [
                'name' => strtolower(trim($city->getName()))
            ];
        }

        return $this->json($data);
    }

    //------------------------------------------------------------------------------------------------------------

    // ajouter une ville

    #[Route('/insertVille', name: 'insertVille', methods: ['POST'])]
    public function addCity(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();

            $name = $request->get('name');
            $postalCode = $request->get('postalCode');

            // On vérifie si les champs sont vides
            if (empty($name) || empty($postalCode)) {
                return $this->json([
                    'message' => 'Tous les champs sont obligatoires'
                ]);
            }

            // On vérifie si la ville existe déjà (on trime et on met en minuscule)
            if ($doctrine->getRepository(City::class)->findOneBy(['name' => strtolower(trim($name))])) {
                return $this->json([
                    'message' => 'La ville existe déjà'
                ]);
            }

            $city = new City();

            $city->setName($name);
            $city->setPostalCode($postalCode);

            $entityManager->persist($city);
            $entityManager->flush();

            return $this->json([
                'message' => 'La ville a bien été ajoutée'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage()         
            ]);
        }
    }

    //------------------------------------------------------------------------------------------------------------

    // supprimer une ville par son id

    #[Route('/deleteVille/{id}', name: 'deleteVille', methods: ['DELETE'])]
    public function deleteCity(Request $request, ManagerRegistry $doctrine, int $id): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();

            $city = $doctrine->getRepository(City::class)->find($id);

            if (!$city) {
                return $this->json([
                    'message' => 'La ville n\'existe pas'
                ]);
            }

            $entityManager->remove($city);
            $entityManager->flush();

            return $this->json([
                'message' => 'La ville a bien été supprimée'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage()         
            ]);
        }
    }
}
