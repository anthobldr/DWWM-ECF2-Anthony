<?php

namespace App\Controller;

use App\Repository\AbsenceRepository;
use App\Repository\TraineeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(TraineeRepository $traineeRepo, AbsenceRepository $absenceRepo, Request $request): Response
    {
        // Charger les trainees
        $trainees = $traineeRepo->findAll();
        
        foreach($trainees as $trainee){
            $trainee->getAbsences()->count();
        }
        
        // Statut présent/absent aujourd'hui
        $today = new \DateTime();
        $traineeStatus = [];
        foreach($trainees as $trainee){
            $absence = $absenceRepo->findOneBy([
                'trainee' => $trainee,
                'date' => $today
            ]);

            $traineeStatus[$trainee->getId()] = [
                'trainee' => $trainee,
                'isAbsent' => $absence !== null,
                'absence' => $absence
            ];
        }
        
        // Stagiaire sélectionné
        $selectedTraineeId = $request->query->get('trainee');
        $selectedTrainee = null;
        $selectedTraineeAbsences = [];
        
        if($selectedTraineeId){
            $selectedTrainee = $traineeRepo->find($selectedTraineeId);
            if($selectedTrainee){
                $selectedTraineeAbsences = $absenceRepo->findBy(['trainee' => $selectedTrainee], ['date' => 'DESC']);
            }
        }

        // Trainees en édition
        $editingTraineeId = $request->query->get('edit_trainee');
        $editingTrainee = null;
        if($editingTraineeId){
            $editingTrainee = $traineeRepo->find($editingTraineeId);
        }

        // Absence en édition
        $editingAbsenceId = $request->query->get('edit_absence');
        $editingAbsence = null;
        if($editingAbsenceId){
            $editingAbsence = $absenceRepo->find($editingAbsenceId);
        }

        return $this->render('admin/dashboard/index.html.twig', [
            'trainees' => $trainees,
            'traineeStatus' => $traineeStatus,
            'selectedTrainee' => $selectedTrainee,
            'selectedTraineeAbsences' => $selectedTraineeAbsences,
            'editingTrainee' => $editingTrainee,
            'editingAbsence' => $editingAbsence,
        ]);
    }
}