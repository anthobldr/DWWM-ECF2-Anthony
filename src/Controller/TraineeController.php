<?php

namespace App\Controller;

use App\Entity\Trainee;
use App\Form\TraineeType;
use App\Repository\TraineeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/trainee')]
final class TraineeController extends AbstractController
{
    #[Route(name: 'app_trainee_index', methods: ['GET'])]
    public function index(TraineeRepository $traineeRepository): Response
    {
        return $this->render('trainee/index.html.twig', [
            'trainees' => $traineeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_trainee_new', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $trainee = new Trainee();
        $trainee->setFirstName($request->request->get('firstName'));
        $trainee->setLastName($request->request->get('lastName'));
        $trainee->setEmail($request->request->get('email'));
        $trainee->setPhone($request->request->get('phone'));

        $photoFile = $request->files->get('photoFilename');
        
        if ($photoFile) {
            $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

            try {
                $photoFile->move($this->getParameter('photos_directory'), $newFilename);
                $trainee->setPhotoFilename($newFilename);
            } catch (FileException $e) {
                return new Response('Erreur upload', 400);
            }
        }

        $entityManager->persist($trainee);
        $entityManager->flush();

        $this->addFlash('success', 'Stagiaire modifié avec succès');
            return $this->redirectToRoute('app_admin_dashboard', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_trainee_show', methods: ['GET'])]
    public function show(Trainee $trainee): Response
    {
        return $this->render('trainee/show.html.twig', [
            'trainee' => $trainee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trainee_edit', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Trainee $trainee, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response {
        // Récupérer les données du formulaire
        $trainee->setLastName($request->request->get('lastName'));
        $trainee->setFirstName($request->request->get('firstName'));
        $trainee->setPhone($request->request->get('phone'));
        $trainee->setEmail($request->request->get('email'));

        // Date de naissance
        if ($request->request->get('dateOfBirth')) {
            $trainee->setDateOfBirth(new \DateTime($request->request->get('dateOfBirth')));
        }

        // Upload nouvelle photo
        $photoFile = $request->files->get('photoFilename');
        if ($photoFile) {
            // Supprimer l'ancienne photo
            if ($trainee->getPhotoFilename()) {
                $oldFile = $this->getParameter('photos_directory') . '/' . $trainee->getPhotoFilename();
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

            try {
                $photoFile->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );
                $trainee->setPhotoFilename($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors de l\'upload');
            }
        }

        $entityManager->flush();

        $this->addFlash('success', 'Stagiaire modifié avec succès');
        return $this->redirectToRoute('app_admin_dashboard', ['trainee' => $trainee->getId()]);
    }

    #[Route('/{id}', name: 'app_trainee_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Trainee $trainee, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trainee->getId(), $request->getPayload()->getString('_token'))) {
            // Supprimer la photo
            if ($trainee->getPhotoFilename()) {
                $photoPath = $this->getParameter('photos_directory') . '/' . $trainee->getPhotoFilename();
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }

            $entityManager->remove($trainee);
            $entityManager->flush();

            $this->addFlash('success', 'Stagiaire supprimé avec succès');
        }

        return $this->redirectToRoute('app_admin_dashboard', [], Response::HTTP_SEE_OTHER);
    }
}