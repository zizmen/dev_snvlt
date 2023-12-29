<?php

namespace App\Form\References;

use App\Entity\References\Essence;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EssencesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero_essence')
            ->add('nom_vernaculaire')
            ->add('famille_essence')
            ->add('nom_scientifique')
            ->add('categorie_essence')
            ->add('taxe_abattage')
            ->add('dm_minima')
            ->add('taxe_preservation')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Essence::class,
        ]);
    }
}
