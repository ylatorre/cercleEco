<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Déclare la classe ChatGPTType pour créer un type de formulaire personnalisé.
class ChatGPTType extends AbstractType
{
    // La méthode buildForm est appelée pour définir les champs du formulaire.
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Ajoute un champ de texte multi-ligne (Textarea) pour la saisie de l'utilisateur.
        // 'userInput' sera l'identifiant du champ dans le formulaire.
        $builder
            ->add('userInput', TextareaType::class, [
                // Définit l'étiquette qui apparaîtra au-dessus du champ, ici "Your question:".
                'label' => 'Your question:',
            ])
            // Ajoute un bouton de soumission (Submit) pour envoyer le formulaire.
            ->add('submit', SubmitType::class, [
                // Définit le texte qui apparaîtra sur le bouton de soumission, ici "Ask".
                'label' => 'Ask',
            ]);
    }

    // La méthode configureOptions permet de définir des options par défaut pour le formulaire.
    public function configureOptions(OptionsResolver $resolver)
    {
        // Définit les options par défaut du formulaire, ici aucune option particulière n'est ajoutée.
        $resolver->setDefaults([]);
    }
}