<?php

namespace App\Form\References;

use App\Entity\References\GrilleLegalite;
use App\Entity\References\Ddef;
use App\Entity\References\Dr;
use App\Entity\References\TypeOperateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class GrilleLegaliteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle_document', TextType::class, [
                'label'=>'Document type',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le document Type est obligatoire',
                    ])
                ]
            ])

            ->add('description_document', TextareaType::class, [
                'label'=>'Description',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control',
                    'style'=>'background-color:lightblue',
                    'readonly'=>true
                ]

            ])
            ->add('periodicite', ChoiceType::class, [
                'label'=>'Périodicité',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'multiple'=>false,
                'expanded'=>false,
                'required'=>true,
                'attr'=>[
                    '           class'=>'form-control periodicite alert-light text-dark',
                    'placeholder'=>'--Sélectionnez la périodicité...'
                ],
                'choices'=>[
                    'MOIS'=>'1',
                    'ANNEE'=>'2',
                    'INDEFINI'=>'3'
                ]

            ])
            ->add('duree', NumberType::class, [
                'label'=>'Duree',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark',
                    'style'=>'background-color:lightyellow'
                ],

            ])
            ->add('statut', ChoiceType::class, [
                'label'=>'Statut',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'multiple'=>false,
                'expanded'=>false,
                'required'=>true,
                'attr'=>[
                    '           class'=>'form-control periodicite alert-light text-dark',
                    'placeholder'=>'--Définir un statut...'
                ],
                'choices'=>[
                    'ACTIF'=>'ACTIF',
                    'INACTIF'=>'INACTIF'
                ]

            ])
            ->add('code_operateur', EntityType::class, [
                'label'=>'Sélectionner la Direction Départementale',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'class'=> TypeOperateur::class,
                'multiple'=>false,
                'expanded'=>false,
                'required'=>true,
                'attr'=>[
                    '           class'=>'form-control operateur alert-light text-dark',
                    'placeholder'=>'--Sélectionnez le type Opérateur...'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'le Type Opérateur est obligatoire',
                    ])

                ],

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GrilleLegalite::class,
        ]);
    }
}
