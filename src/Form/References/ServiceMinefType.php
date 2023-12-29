<?php

namespace App\Form\References;

use App\Entity\References\Direction;
use App\Entity\References\ServiceMinef;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\NotBlank;

class ServiceMinefType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle_service', TextType::class, [
                'label'=>'Renseignez la dénomination du service',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nopm du service est obligatoire',
                    ])
                ]
            ])
            ->add('sigle', TextType::class, [
                'label'=>'Sigle',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark'
                ]
            ])
            ->add('telephone_service', TextType::class, [
                'label'=>'N° Téléphone',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark'
                ]
            ])

            ->add('codeservice', TextType::class, [
                'label'=>'Renseignez la code du service (délivré par le MINEF)',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
        '           class'=>'form-control alert-light text-dark'
                ]
            ])

            ->add('emailPersonneRessource', TextType::class, [
                'label'=>'Email Personne Ressource',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-danger',
                    'style'=>'font-weight:bold;font-size:18px;',
                    'readonly'=>true
                ]

            ])

            ->add('personneRessource', TextType::class, [
                'label'=>'Personne ressource',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-danger',
                    'style'=>'font-weight:bold;font-size:18px;',
                    'readonly'=>true
                ]

            ])

            ->add('mobilePersonneRessource', TextType::class, [
                'label'=>'Mobile Personne ressource',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-danger',
                    'style'=>'font-weight:bold;font-size:18px;',
                    'readonly'=>true
                ]

            ])

            ->add('code_direction', EntityType::class, [
                'class'=>Direction::class,
                'label'=>'Sélectionnez la Direction',
                'placeholder'=>'-- Sélectionnez la direction --',
                'multiple'=>false,
                'expanded'=>false,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark direction'
                ]

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ServiceMinef::class,
        ]);
    }
}
