<?php

namespace App\Controller;

use App\Entity\Travel;
use App\Entity\City;
use App\Entity\UserInformation;

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
                'startingCity' => $travel->getStartingCity()->getName(),
                'arrivalCity' => $travel->getArrivalCity()->getName(),
                'driver' => $travel->getDriver()->getFirstName() . ' ' . $travel->getDriver()->getLastName(),
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

            // On récupère le manager de Doctrine
            $entityManager = $doctrine->getManager();

            // On récupère l'entité City
            $startingCity = $doctrine->getRepository(City::class)->find($request->get('startingCity'));
            $arrivalCity = $doctrine->getRepository(City::class)->find($request->get('arrivalCity'));

            // On récupère le chauffeur
            $driver = $doctrine->getRepository(UserInformation::class)->find($request->get('driver'));

            $DateOfDeparture = $request->get('DateOfDeparture');
            $arrivalDate = $request->get('arrivalDate');
            $kilometer = $request->get('kilometer');
           
            // On vérifie si les champs sont vides
            if (empty($DateOfDeparture) || empty($arrivalDate) || empty($kilometer) || empty($startingCity) || empty($arrivalCity) || empty($driver)) {
                return $this->json([
                    'message' => 'Tous les champs sont obligatoires'
                ]);
            }

            $travel = new Travel();
            $date   = new DateTime();

            // On formate les dates au format français
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
            
            $startingCity = $doctrine->getRepository(City::class)->find($startingCity);
            $arrivalCity = $doctrine->getRepository(City::class)->find($arrivalCity);
        

            if (!$startingCity || !$arrivalCity) {
                return $this->json([
                    'message' => 'La ville de départ et/ou d\'arrivée n\'existe pas'
                ]);
            }

            // On vérifie si le chauffeur existe, s'il est disponible et s'il a bien un véhicule
            if (!$driver) {
                return $this->json([
                    'message' => 'Ce chauffeur n\'existe pas'
                ]);
            }

            // On vérifie si l'utilisateur à bien un véhicule
            if (!$driver->getVehicle()) {
                return $this->json([
                    'message' => 'Ce chauffeur n\'a pas de véhicule'
                ]);
            }

                        
            $travel->setStartingCity($startingCity);
            $travel->setArrivalCity($arrivalCity);

            $travel->setDriver($driver);

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
  
    // récupérer tous les conducteurs 

    #[Route('/listeInscriptionDriver', name: 'listeInscriptionDriver')]
    public function listDriver(ManagerRegistry $doctrine): JsonResponse
    {
        $drivers = $doctrine->getRepository(UserInformation::class)->findAll();

        $data = [];

        foreach ($drivers as $driver) {

            // On vérifie si le conducteur a un véhicule
            if (!$driver->getVehicle()) {
                continue;
            }
            $data[] = [
                'id' => $driver->getId(),
                'firstName' => $driver->getFirstName(),
                'lastName' => $driver->getLastName(),
                'email' => $driver->getEmail(),
                'phone' => $driver->getPhone(),     
            ];
        }

        return $this->json($data);
    }
    //------------------------------------------------------------------------------------------------------------

    // récupérer un conducteur grâce à l'id trajet

    //------------------------------------------------------------------------------------------------------------

    // récupérer tous les passagers

    //------------------------------------------------------------------------------------------------------------

    // supprimer un passager

    //------------------------------------------------------------------------------------------------------------

    // ajouter un passager à un trajet

    //------------------------------------------------------------------------------------------------------------

    // récupérer tous les personnes inscrites à un trajet

    //------------------------------------------------------------------------------------------------------------
        

}
