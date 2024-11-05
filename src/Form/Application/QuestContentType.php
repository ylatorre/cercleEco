<?php

namespace App\Form\Application;

use App\Entity\Application\Quest;
use App\Entity\Application\QuestContent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestContentType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu')
            ->add('quest', EntityType::class, [
                'class' => Quest::class,
                'choice_label' => function (Quest $quest) {
                    return $quest->getNom(); // Affiche le nom de la Quest au lieu de l'ID
                },
                'placeholder' => 'Sélectionnez une quête',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuestContent::class,
        ]);
    }
}
