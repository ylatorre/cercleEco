<?php

namespace App\Controller\Application;

use App\Entity\Application\Quest;
use App\Form\Application\QuestType;
use App\Repository\Application\QuestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/application/quest')]
final class QuestController extends AbstractController
{
    #[Route(name:'app_application_quest_index', methods: ['GET'])]
    public function index(QuestRepository $questRepository): Response
    {
        return $this->render('application/quest/index.html.twig', [
            'quests' => $questRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_application_quest_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quest = new Quest();
        $form = $this->createForm(QuestType::class, $quest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quest);
            $entityManager->flush();

            return $this->redirectToRoute('app_application_quest_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('application/quest/new.html.twig', [
            'quest' => $quest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_application_quest_show', methods: ['GET'])]
    public function show(Quest $quest): Response
    {
        return $this->render('application/quest/show.html.twig', [
            'quest' => $quest,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_application_quest_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quest $quest, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuestType::class, $quest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_application_quest_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('application/quest/edit.html.twig', [
            'quest' => $quest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_application_quest_delete', methods: ['POST'])]
    public function delete(Request $request, Quest $quest, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quest->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($quest);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_application_quest_index', [], Response::HTTP_SEE_OTHER);
    }
}
