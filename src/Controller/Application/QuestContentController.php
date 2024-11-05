<?php

namespace App\Controller\Application;

use App\Entity\Application\QuestContent;
use App\Form\Application\QuestContentType;
use App\Repository\Application\QuestContentRepository;
use App\Repository\Application\QuestRepository; // Assurez-vous que cette ligne est correcte
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/application/quest/content')]
final class QuestContentController extends AbstractController
{
    #[Route(name: 'app_application_quest_content_index', methods: ['GET'])]
    public function index(QuestContentRepository $questContentRepository): Response
    {
        return $this->render('application/quest_content/index.html.twig', [
            'quest_contents' => $questContentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_application_quest_content_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, QuestRepository $questRepository): Response
    {
        $questContent = new QuestContent();
        $form = $this->createForm(QuestContentType::class, $questContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($questContent);
            $entityManager->flush();

            return $this->redirectToRoute('app_application_quest_content_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('application/quest_content/new.html.twig', [
            'quest_content' => $questContent,
            'form' => $form->createView(),
            // Ajoutez la liste des Quests ici si besoin
            'quests' => $questRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_application_quest_content_show', methods: ['GET'])]
    public function show(QuestContent $questContent): Response
    {
        return $this->render('application/quest_content/show.html.twig', [
            'quest_content' => $questContent,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_application_quest_content_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, QuestContent $questContent, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuestContentType::class, $questContent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_application_quest_content_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('application/quest_content/edit.html.twig', [
            'quest_content' => $questContent,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_application_quest_content_delete', methods: ['POST'])]
    public function delete(Request $request, QuestContent $questContent, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$questContent->getId(), $request->request->get('_token'))) {
            $entityManager->remove($questContent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_application_quest_content_index', [], Response::HTTP_SEE_OTHER);
    }
}
