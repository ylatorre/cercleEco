<?php

namespace App\Controller\Application;

use App\Entity\Application\DayQuest;
use App\Form\Application\DayQuestType;
use App\Repository\Application\DayQuestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/administration/day/quest')]
final class DayQuestController extends AbstractController
{
    #[Route(name: 'app_application_day_quest_index', methods: ['GET'])]
    public function index(DayQuestRepository $dayQuestRepository): Response
    {
        return $this->render('application/day_quest/index.html.twig', [
            'day_quests' => $dayQuestRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_application_day_quest_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dayQuest = new DayQuest();
        $form = $this->createForm(DayQuestType::class, $dayQuest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($dayQuest);
            $entityManager->flush();

            return $this->redirectToRoute('app_application_day_quest_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('application/day_quest/new.html.twig', [
            'day_quest' => $dayQuest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_application_day_quest_show', methods: ['GET'])]
    public function show(DayQuest $dayQuest): Response
    {
        return $this->render('application/day_quest/show.html.twig', [
            'day_quest' => $dayQuest,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_application_day_quest_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DayQuest $dayQuest, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DayQuestType::class, $dayQuest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_application_day_quest_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('application/day_quest/edit.html.twig', [
            'day_quest' => $dayQuest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_application_day_quest_delete', methods: ['POST'])]
    public function delete(Request $request, DayQuest $dayQuest, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dayQuest->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($dayQuest);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_application_day_quest_index', [], Response::HTTP_SEE_OTHER);
    }
}
