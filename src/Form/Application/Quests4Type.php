<?php

namespace App\Form\Application;

use App\Entity\Application\Quests;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Quests4Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('CreatedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('level')
            ->add('contenu')
            ->add('question')
            ->add('reponseA')
            ->add('reponseB')
            ->add('reponseC')
            ->add('reponseD')
            ->add('reponseCorrecte')
            ->add('ordre')
            ->add('xp_give')
            ->add('token')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quests::class,
        ]);
    }
}
