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

    #[Route('/{id}/edit', name: 'app_trainee_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Trainee $trainee, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(TraineeType::class, $trainee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photoFilename')->getData();
            
            if ($photoFile) {
                // Supprimer l'ancienne photo si elle existe
                if ($trainee->getPhotoFilename()) {
                    $oldPhoto = $this->getParameter('photos_directory') . '/' . $trainee->getPhotoFilename();
                    if (file_exists($oldPhoto)) {
                        unlink($oldPhoto);
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
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de la photo');
                }

                $trainee->setPhotoFilename($newFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Stagiaire modifié avec succès');
            return $this->redirectToRoute('app_admin_dashboard', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trainee/edit.html.twig', [
            'trainee' => $trainee,
            'form' => $form,
        ]);
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