<?php

namespace App\Controller;

use App\Entity\Absence;
use App\Form\AbsenceType;
use App\Repository\AbsenceRepository;
use App\Repository\TraineeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/absence')]
final class AbsenceController extends AbstractController
{
    #[Route(name: 'app_absence_index', methods: ['GET'])]
    public function index(AbsenceRepository $absenceRepository): Response
    {
        return $this->render('absence/index.html.twig', [
            'absences' => $absenceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_absence_new', methods: ['GET', 'POST'])]
    public function create(Request $request, TraineeRepository $traineeRepo, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response {
        // Récupérer les données du formulaire
        $traineeId = $request->request->get('trainee_id');
        $startDate = $request->request->get('start_date');
        $reason = $request->request->get('reason');
        $notes = $request->request->get('notes');

        // Vérifier que le stagiaire existe
        $trainee = $traineeRepo->find($traineeId);
        if (!$trainee) {
            $this->addFlash('error', 'Stagiaire introuvable');
            return $this->redirectToRoute('app_admin_dashboard');
        }

        // Créer l'absence
        $absence = new Absence();
        $absence->setTrainee($trainee);
        $absence->setDate(new \DateTime($startDate));
        $absence->setReason($reason);
        $absence->setNotes($notes);

        // Gérer le fichier justificatif (optionnel)
        $justificativeFile = $request->files->get('justificative');
        if ($justificativeFile) {
            $originalFilename = pathinfo($justificativeFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $justificativeFile->guessExtension();

            try {
                $justificativeFile->move(
                    $this->getParameter('justificatives_directory'),
                    $newFilename
                );
                $absence->setJustificativeFilename($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors de l\'upload du justificatif');
            }
        }

        // Sauvegarder en base de données
        $entityManager->persist($absence);
        $entityManager->flush();

        $this->addFlash('success', 'Absence enregistrée avec succès');
        return $this->redirectToRoute('app_admin_dashboard', ['trainee' => $traineeId]);
    }

    #[Route('/{id}', name: 'app_absence_show', methods: ['GET'])]
    public function show(Absence $absence): Response
    {
        return $this->render('absence/show.html.twig', [
            'absence' => $absence,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_absence_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Absence $absence, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AbsenceType::class, $absence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_absence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('absence/edit.html.twig', [
            'absence' => $absence,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_absence_delete', methods: ['POST'])]
    public function delete(Request $request, Absence $absence, EntityManagerInterface $entityManager): Response
    {
        $traineeId = $absence->getTrainee()->getId();

        // Supprimer le fichier justificatif
        if ($absence->getJustificativeFilename()) {
            $file = $this->getParameter('justificatives_directory') . '/' . $absence->getJustificativeFilename();
            if (file_exists($file)) {
                unlink($file);
            }
        }

        if ($this->isCsrfTokenValid('delete'.$absence->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($absence);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Absence supprimée');
        return $this->redirectToRoute('app_admin_dashboard', ['trainee' => $traineeId]);
    }
}
