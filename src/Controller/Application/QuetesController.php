<?php

namespace App\Controller\Application;

use App\Entity\Application\Quetes;
use App\Entity\Application\QuetesReponses;
use App\Form\Application\QuetesType;
use App\Repository\Application\QuetesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/application/quetes')]
final class QuetesController extends AbstractController
{
    #[Route(name: 'app_application_quetes_index', methods: ['GET'])]
    public function index(QuetesRepository $quetesRepository): Response
    {
        return $this->render('application/quetes/index.html.twig', [
            'quetes' => $quetesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_application_quetes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quete = new Quetes();
        $form = $this->createForm(QuetesType::class, $quete);
        $form->handleRequest($request);

        $params = $request->request->all();

        if ($form->isSubmitted() && $form->isValid()) {
            $quete->setEtat(0);
            $entityManager->persist($quete);
            $entityManager->flush();
            if(array_key_exists('reponses', $params)){
                foreach($params['reponses'] as $p){
                    $quetesReponses = new QuetesReponses();
                    $quetesReponses->setQuete($quete);
                    $quetesReponses->setTitre($p['titre']);
                    if($p['isGoodReponse'] == 'yes'){
                        $quetesReponses->setIsGoodQuestion(1);
                    }else{
                        $quetesReponses->setIsGoodQuestion(0);
                    }
                    $entityManager->persist($quetesReponses);
                }
            }

            $entityManager->persist($quete);
            $entityManager->flush();

            return $this->redirectToRoute('app_application_quetes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('application/quetes/new.html.twig', [
            'quete' => $quete,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_application_quetes_show', methods: ['GET'])]
    public function show(Quetes $quete, EntityManagerInterface $entityManager): Response
    {
        $quetesReponses = $entityManager->getRepository(QuetesReponses::class)->findByQuete($quete);
        return $this->render('application/quetes/show.html.twig', [
            'quete' => $quete,
            'reponses' => $quetesReponses,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_application_quetes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quetes $quete, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuetesType::class, $quete);
        $form->handleRequest($request);
        $quete = $entityManager->getRepository(Quetes::class)->find($quete->getId());
        $tab = $quete->getQuetesReponses();
        foreach($tab as $t){
            $reponses[] = $t;
        }
        $params = $request->request->all();

        if ($form->isSubmitted() && $form->isValid()) {
            if(array_key_exists('reponsesOld', $params)){
                foreach($params['reponsesOld'] as $p){
                    $quetesReponses = $entityManager->getRepository(QuetesReponses::class)->findOneById($p['id']);
                    $quetesReponses->setTitre($p['titre']);
                    if($p['isGoodQuestion'] == 'yes'){
                        $quetesReponses->setIsGoodQuestion(1);
                    }else{
                        $quetesReponses->setIsGoodQuestion(0);
                    }
                    $entityManager->persist($quetesReponses);
                }
            }
            if(array_key_exists('reponses', $params)){
                foreach($params['reponses'] as $p){
                    $quetesReponses = new QuetesReponses();
                    $quetesReponses->setQuete($quete);
                    $quetesReponses->setTitre($p['titre']);
                    if($p['isGoodReponse'] == 'yes'){
                        $quetesReponses->setIsGoodQuestion(1);
                    }else{
                        $quetesReponses->setIsGoodQuestion(0);
                    }
                    $entityManager->persist($quetesReponses);
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_application_quetes_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('application/quetes/edit.html.twig', [
            'quete' => $quete,
            'form' => $form,
            'reponses' => $reponses,
        ]);
    }

    #[Route('/{id}', name: 'app_application_quetes_delete', methods: ['POST'])]
    public function delete(Request $request, Quetes $quete, EntityManagerInterface $entityManager): Response
    {
        $quetesReponses = $entityManager->getRepository(QuetesReponses::class)->findByQuete($quete);
        if ($this->isCsrfTokenValid('delete'.$quete->getId(), $request->getPayload()->getString('_token'))) {
            foreach($quetesReponses as $reponse){
                $entityManager->remove($reponse);
            }
            $entityManager->remove($quete);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_application_quetes_index', [], Response::HTTP_SEE_OTHER);
    }
}
