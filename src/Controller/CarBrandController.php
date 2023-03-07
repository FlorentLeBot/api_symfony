<?php

namespace App\Controller;

use App\Entity\CarBrand;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


// methode : listBrand, addBrand, updateBrand, deleteBrand

class CarBrandController extends AbstractController
{
    #[Route('/listeMarque', name: 'listeMarque', methods: ['GET'])]
    public function listBrand(ManagerRegistry $doctrine): JsonResponse
    {
        $carBrands = $doctrine->getRepository(CarBrand::class)->findAll();
        $data = [];

        foreach ($carBrands as $brand) {
            $data[] = [
                'id' => $brand->getId(),
                'name' => strtolower(trim($brand->getName()))
            ];
        }

        return $this->json($data);
    }

    //------------------------------------------------------------------------------------------------------------

    #[Route('/insertMarque', name: 'insertMarque', methods: ['POST'])]
    public function addBrand(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {

            // On récupère le manager de Doctrine
            $entityManager = $doctrine->getManager();

            // On récupère le nom de la marque de voiture
            $name = $request->get('name');

            // On vérifie si le nom de la marque est vide
            if (empty($name)) {
                return $this->json([
                    'message' => 'Le nom de la marque est vide'
                ]);
            }

            // On vérifie si la marque existe déjà (on trime et on met en minuscule)
            if ($doctrine->getRepository(CarBrand::class)->findOneBy(['name' => strtolower(trim($name))])) {
                return $this->json([
                    'message' => 'La marque existe déjà'
                ]);
            }
            // Sinon on l'ajoute toujours en minuscule et sans espace
            else {
                // On crée une nouvelle marque de voiture
                $carBrand = new CarBrand();
                $carBrand->setName(strtolower(trim($name)));

                // On ajoute la marque de voiture en base de données
                $entityManager->persist($carBrand);
                $entityManager->flush();

                return $this->json([
                    'message' => 'La marque a bien été ajoutée'
                ]);
            }

        } catch (\Exception $e) {
            return $this->json([
                // On retourne un message d'erreur en fonction de l'erreur
                'message' => $e->getMessage()
            ]);
        }
    }

    //------------------------------------------------------------------------------------------------------------

    #[Route('/updateMarque/{id}', name: 'updateMarque', methods: ['PUT'])]
    public function updateBrand(Request $request, ManagerRegistry $doctrine, int $id): JsonResponse
    {  
        try {

            // On récupère le manager de Doctrine
            $entityManager = $doctrine->getManager();

            // On récupère l'id de la marque de voiture
            $id = $request->get('id');

            // On récupère le nom de la marque de voiture
            $name = $request->get('name');

            // On vérifie si le nom de la marque est vide
            if (empty($name)) {
                return $this->json([
                    'message' => 'Le nom de la marque est vide'
                ]);
            }

            // On récupère la marque de voiture
            $carBrandRepository = $doctrine->getRepository(CarBrand::class);
            $carBrand = $carBrandRepository->find($id);

            // Si la marque n'existe pas, on retourne un message d'erreur
            if (!$carBrand) {
                return $this->json([
                    'message' => 'La marque n\'existe pas'
                ]);
            }

            // Sinon, on modifie la marque de voiture
            else {
                // On set le nom de la marque de voiture
                $carBrand->setName($name);

                // On ajoute la marque de voiture en base de données
                $entityManager->persist($carBrand);

                // On envoie les données en base de données
                $entityManager->flush();
            }

            // On retourne un message de succès
            return $this->json([
                'message' => 'La marque a bien été modifiée'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                // On retourne un message d'erreur en fonction de l'erreur
                'message' => $e->getMessage()
            ]);
        }
    }

    //------------------------------------------------------------------------------------------------------------

    #[Route('/deleteMarque/{id}', name: 'deleteMarque', methods: ['DELETE'])]
    public function deleteBrand(Request $request, ManagerRegistry $doctrine, int $id): JsonResponse
    {
        try {

            // On récupère le manager de Doctrine
            $entityManager = $doctrine->getManager();

            // On récupère l'id de la marque de voiture
            $id = $request->get('id');

            // On récupère la marque de voiture
            $carBrandRepository = $doctrine->getRepository(CarBrand::class);
            $carBrand = $carBrandRepository->find($id);

            // Si la marque n'existe pas, on retourne un message d'erreur
            if (!$carBrand) {
                return $this->json([
                    'message' => 'La marque n\'existe pas'
                ]);
            }

            // On supprime la marque de voiture
            $entityManager->remove($carBrand);

            // On envoie les données en base de données
            $entityManager->flush();

            // On retourne un message de succès
            return $this->json([
                'message' => 'La marque a bien été supprimée'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                // On retourne un message d'erreur en fonction de l'erreur
                'message' => $e->getMessage()
            ]);
        }
    }
}
