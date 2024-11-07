<?php

namespace App\Controller\Application;

use App\Entity\Application\Dons;
use App\Form\Application\DonsType;
use App\Repository\Application\DonsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/administration/dons')]
final class DonsController extends AbstractController
{
    #[Route(name: 'app_application_dons_index', methods: ['GET'])]
    public function index(DonsRepository $donsRepository): Response
    {
        return $this->render('Application/dons/index.html.twig', [
            'dons' => $donsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_application_dons_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $don = new Dons();
        $form = $this->createForm(DonsType::class, $don);
        $form->handleRequest($request);
        $entityManager->persist($don);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = $don->getToken().'.'.$imageFile->guessExtension();
                $path = $this->getParameter('kernel.project_dir').'/public/image/dons';

                try {
                    $imageFile->move(
                        $path,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // handle exception if something happens during file upload
                }

                $don->setImage($newFilename);
            }

            $entityManager->persist($don);
            $entityManager->flush();

            return $this->redirectToRoute('app_application_dons_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Application/dons/new.html.twig', [
            'don' => $don,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_application_dons_show', methods: ['GET'])]
    public function show(Dons $don): Response
    {
        return $this->render('Application/dons/show.html.twig', [
            'don' => $don,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_application_dons_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dons $don, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DonsType::class, $don);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = $don->getToken().'.'.$imageFile->guessExtension();
                $path = $this->getParameter('kernel.project_dir').'/public/image/dons';

                try {
                    $imageFile->move(
                        $path,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // handle exception if something happens during file upload
                }

                $don->setImage($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_application_dons_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Application/dons/edit.html.twig', [
            'don' => $don,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_application_dons_delete', methods: ['POST'])]
    public function delete(Request $request, Dons $don, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$don->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($don);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_application_dons_index', [], Response::HTTP_SEE_OTHER);
    }
}
