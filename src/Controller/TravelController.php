<?php

namespace App\Controller;

use App\Entity\Travel;
use App\Entity\City;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use DateTime;


class TravelController extends AbstractController
{
    #[Route('/listeTrajet', name: 'listeTrajet')]
    public function listTravel(ManagerRegistry $doctrine): JsonResponse
    {
        // liste des trajets de l'entité Travel

        $travels = $doctrine->getRepository(Travel::class)->findAll();

        $data = [];

        // On récupère les dates de départ et d'arrivée au format français

        foreach ($travels as $travel) {
            $data[] = [
                'id' => $travel->getId(),
                'DateOfDeparture' => $travel->getDateOfDeparture()->format('d/m/Y à H\hi'),
                'arrivalDate' => $travel->getArrivalDate()->format('d/m/Y à H\hi'),
                'kilometer' => $travel->getKilometer(),
                'startingCity' => $travel->getStartingCity(),
                'arrivalCity' => $travel->getArrivalCity(),
            ];
        }

        return $this->json($data);
    }

    //------------------------------------------------------------------------------------------------------------

    // ajouter un trajet

    #[Route('/insertTrajet', name: 'insertTrajet')]
    public function addTravel(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();

            $DateOfDeparture = $request->get('DateOfDeparture');
            $arrivalDate = $request->get('arrivalDate');
            $kilometer = $request->get('kilometer');
            $startingCity = $request->get('startingCity');
            $arrivalCity = $request->get('arrivalCity');


            // On vérifie si les champs sont vides
            if (empty($DateOfDeparture) || empty($arrivalDate) || empty($kilometer) || empty($startingCity) || empty($arrivalCity)) {
                return $this->json([
                    'message' => 'Tous les champs sont obligatoires'
                ]);
            }

            $travel = new Travel();
            $date   = new DateTime();


            $DateOfDeparture = $date->createFromFormat('d/m/Y à H\hi', $DateOfDeparture);
            $arrivalDate = $date->createFromFormat('d/m/Y à H\hi', $arrivalDate);

            // On vérifie si la date de départ est supérieure à la date d'arrivée
            if ($DateOfDeparture > $arrivalDate) {
                return $this->json([
                    'message' => 'La date de départ doit être inférieure à la date d\'arrivée'
                ]);
            }

            
            // On vérifie si le nombre de kilomètres n'est pas un nombre décimal ou un string
            if (!is_numeric($kilometer)) {
                return $this->json([
                    'message' => 'Le nombre de kilomètres doit être un nombre entier'
                ]);
            }

            // On vérifie si la date est bien au format français
            if (!$DateOfDeparture || !$arrivalDate) {
                return $this->json([
                    'message' => 'La date doit être au format français (jj/mm/aaaa à hh\hi) ex: 01/01/2021 à 12\00'
                ]);
            }

            // On vérifie si la ville de départ et d'arrivée sont différentes
            if ($startingCity == $arrivalCity) {
                return $this->json([
                    'message' => 'La ville de départ et d\'arrivée doivent être différentes'
                ]);
            }

            // On vérifie que la ville de départ et la ville d'arrivé existent bien grâce à leurs ids

            // Il ne faut pas que les attributs deviennent __isCloning car sinon on ne peut plus les utiliser
            
            $startingCity = $doctrine->getRepository(City::class)->find($startingCity);
            $arrivalCity = $doctrine->getRepository(City::class)->find($arrivalCity);
            

            if (!$startingCity || !$arrivalCity) {
                return $this->json([
                    'message' => 'La ville de départ et/ou d\'arrivée n\'existe pas'
                ]);
            }

            $travel->setStartingCity($startingCity);
            $travel->setArrivalCity($arrivalCity);

            $travel->setDateOfDeparture($DateOfDeparture);
            $travel->setArrivalDate($arrivalDate);
            $travel->setKilometer($kilometer);

            $entityManager->persist($travel);
            $entityManager->flush();

            return $this->json([
                'message' => 'Le trajet a bien été ajouté'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage()
            ]);
        }
    }


    //------------------------------------------------------------------------------------------------------------

    // supprimer un trajet

    #[Route('/deleteTrajet/{id}', name: 'deleteTrajet')]
    public function deleteTravel(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();
            $travel = $doctrine->getRepository(Travel::class)->find($id);

            if (!$travel) {
                return $this->json([
                    'message' => 'Ce trajet n\'existe pas'
                ]);
            }

            $entityManager->remove($travel);
            $entityManager->flush();

            return $this->json([
                'message' => 'Le trajet a bien été supprimé'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Une erreur est survenue'
            ]);
        } 
    }

    //------------------------------------------------------------------------------------------------------------
  
}
