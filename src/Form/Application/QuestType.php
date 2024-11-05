<?php

namespace App\Form\Application;

use App\Entity\Application\Quest;
use App\Entity\Application\QuestContent; // N'oubliez pas d'importer QuestContent
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('DateDeCreation', null, [
                'widget' => 'single_text',
            ])
            ->add('niveau')
            ->add('addContent', CheckboxType::class, [
                'required' => false,
                'label' => 'Ajouter du contenu supplémentaire',
            ])
            ->add('contenuSupplementaire', TextType::class, [
                'required' => false,
                'label' => 'Contenu Supplémentaire',
                'attr' => ['placeholder' => 'Entrez le contenu ici...'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quest::class,
        ]);
    }
}
