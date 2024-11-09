<?php

namespace App\Controller\Front;

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

#[Route('/front')]
final class AjoutDonsController extends AbstractController
{
    #[Route('/new', name: 'app_front_dons_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $don = new Dons();
        $form = $this->createForm(DonsType::class, $don, [
            'user' => $this->getUser(), // Pass the current user to the form
        ]);
        $form->handleRequest($request);
        $entityManager->persist($don);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            $don->setEtat(0);
            $don->setUser($this->getUser());

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

            return $this->redirectToRoute('app_dons', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/donsCrud/new.html.twig', [
            'don' => $don,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{token}/edit', name: 'app_front_dons_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $don = $entityManager->getRepository(Dons::class)->findOneBy(['token' => $request->get('token')]);
        $form = $this->createForm(DonsType::class, $don, [
            'user' => $this->getUser(), // Pass the current user to the form
        ]);
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

            return $this->redirectToRoute('app_dons', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Front/donsCrud/edit.html.twig', [
            'don' => $don,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{token}', name: 'app_front_dons_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        $don = $entityManager->getRepository(Dons::class)->findOneBy(['token' => $request->get('token')]);
        if ($this->isCsrfTokenValid('delete'.$don->getToken(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($don);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dons', [], Response::HTTP_SEE_OTHER);
    }
}
