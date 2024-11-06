<?php

namespace App\Controller\Application;

use App\Form\ChatGPTType;
use App\Service\ChatGPTService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


// Cette classe gère les requêtes HTTP pour une page de chat avec un formulaire.
class ChatGPTController extends AbstractController
{
    

    // Déclare une propriété privée pour stocker l'instance du service ChatGPTService.
    private $chatGPTService;

    // Constructeur de la classe, qui utilise l'injection de dépendance pour passer une
    // instance de ChatGPTService au contrôleur. Cela permet d'accéder à ce service
    // à travers la propriété $chatGPTService dans la classe.
    public function __construct(ChatGPTService $chatGPTService)
    {
        $this->chatGPTService = $chatGPTService;
    }

    #[Route(path: '/chat', name: 'chat_gpt')]
    public function chat(Request $request): Response
    {
        // Crée un formulaire basé sur la classe ChatGPTType, qui définit les champs de ce formulaire.
        $form = $this->createForm(ChatGPTType::class);

        // Traite la requête HTTP actuelle pour pré-remplir le formulaire avec les données
        // soumises (si c'est une requête POST), et pour vérifier si le formulaire a été soumis.
        $form->handleRequest($request);

        // Initialise une variable pour stocker la réponse qui sera affichée après l'envoi du formulaire.
        $responseText = '';


        
        // Vérifie si le formulaire a été soumis et est valide.
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données du formulaire sous forme de tableau.
            $data = $form->getData();
            // Récupère la valeur du champ 'userInput', soit la question de l'utilisateur.
            $userInput = $data['userInput'];
            // Appelle le service ChatGPTService pour générer une réponse à partir de l'entrée utilisateur.
            // La réponse générée est stockée dans la variable $responseText.
            $responseText = $this->chatGPTService->generateResponse($userInput);
        }

        // Retourne une réponse HTTP en utilisant un template Twig pour afficher la page.
        // Le tableau associatif passe des variables au template :
        // 'form' contient la vue du formulaire pour l'afficher,
        // 'responseText' contient la réponse générée, qui sera affichée dans le template.
        return $this->render('Application/chat.html.twig', [
            'form' => $form->createView(),
            'responseText' => $responseText,
        ]);
    }
}
