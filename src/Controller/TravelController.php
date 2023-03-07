<?php

namespace App\Controller;

use App\Entity\Travel;

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
                'kilometer' => $travel->getKilometer()
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


            // On vérifie si les champs sont vides
            if (empty($DateOfDeparture) || empty($arrivalDate) || empty($kilometer)) {
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

            // On vérifie si le nombre de kilomètres est un entier et il doit être supérieur à 0
            if (!is_int($kilometer) && $kilometer < 0) {

                return $this->json([
                    'message' => 'Le nombre de kilomètres doit être un entier et supérieur à 0'
                ]);
            }
            // On vérifie si le nombre de kilomètres n'est pas un nombre décimal ou un string
            if (is_float($kilometer) || is_string($kilometer)) {
                return $this->json([
                    'message' => 'Le nombre de kilomètres doit être un entier et supérieur à 0'
                ]);
            }

            // On vérifie si la date est bien au format français
            if (!$DateOfDeparture || !$arrivalDate) {
                return $this->json([
                    'message' => 'La date doit être au format français (jj/mm/aaaa à hh\hi) ex: 01/01/2021 à 12\00'
                ]);
            }

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
                'message' => 'Une erreur est survenue'
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
