<?php

namespace App\Controller;

use App\Repository\TraineeRepository;
use App\Repository\AbsenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class StatsController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/stats', name: 'app_stats')]
    public function index(TraineeRepository $traineeRepo, AbsenceRepository $absenceRepo, AuthenticationUtils $authenticationUtils): Response
    {
        $trainees = $traineeRepo->findAll();
        
        // Charger les absences
        foreach($trainees as $trainee){
            $trainee->getAbsences()->count();
        }
        
        // Statistiques générales
        $totalAbsences = $absenceRepo->count([]);
        $costPerDay = 712 / 21; // 33.90€ par jour
        $totalLoss = 0;
        
        // Données pour le classement
        $traineesStats = [];
        
        foreach($trainees as $trainee){
            $absenceCount = $trainee->getAbsenceCount();
            $unauthorizedCount = $trainee->getUnauthorizedAbsenceCount();
            $loss = $absenceCount * $costPerDay;
            $totalLoss += $loss;
            
            $traineesStats[] = [
                'id' => $trainee->getId(),
                'firstName' => $trainee->getFirstName(),
                'lastName' => $trainee->getLastName(),
                'photoFilename' => $trainee->getPhotoFilename(),
                'absenceCount' => $absenceCount,
                'unauthorizedAbsenceCount' => $unauthorizedCount,
                'loss' => $loss,
                'hasHighUnauthorized' => $unauthorizedCount > 5
            ];
        }
        
        // Trier par nombre d'absences (DESC)
        usort($traineesStats, function($a, $b) {
            return $b['absenceCount'] - $a['absenceCount'];
        });

        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('stats/index.html.twig', [
            'traineesStats' => $traineesStats,
            'totalTrainees' => count($trainees),
            'totalAbsences' => $totalAbsences,
            'totalLoss' => $totalLoss,
            'costPerDay' => $costPerDay,
            'error' => $error,
        ]);
    }
}