<?php

namespace App\Controller\Application;

use App\Entity\Application\Quests;
use App\Form\Application\Quests4Type;
use App\Repository\Application\QuestsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/application/quests')]
final class QuestsController extends AbstractController
{
    #[Route(name: 'app_application_quests_index', methods: ['GET'])]
    public function index(QuestsRepository $questsRepository): Response
    {
        return $this->render('application/quests/index.html.twig', [
            'quests' => $questsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_application_quests_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quest = new Quests();
        $form = $this->createForm(Quests4Type::class, $quest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quest);
            $entityManager->flush();

            return $this->redirectToRoute('app_application_quests_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('application/quests/new.html.twig', [
            'quest' => $quest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_application_quests_show', methods: ['GET'])]
    public function show(Quests $quest): Response
    {
        return $this->render('application/quests/show.html.twig', [
            'quest' => $quest,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_application_quests_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quests $quest, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Quests4Type::class, $quest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_application_quests_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('application/quests/edit.html.twig', [
            'quest' => $quest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_application_quests_delete', methods: ['POST'])]
    public function delete(Request $request, Quests $quest, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quest->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($quest);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_application_quests_index', [], Response::HTTP_SEE_OTHER);
    }
}
