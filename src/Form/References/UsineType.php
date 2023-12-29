<?php

namespace App\Form\References;

use App\Entity\References\Cantonnement;
use App\Entity\References\TypeTransformation;
use App\Entity\References\Usine;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UsineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero_usine', NumberType::class, [
                'label'=>'Renseignez le N° de l\'usine',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le numéro usine est obligatoire',
                    ])
                ],
                'required'=>true
            ])

            ->add('raison_sociale_usine', TextType::class, [
                'label'=>'Raison sociale° de l\'usine',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseignez la raison sociale° de l\'usine',
                    ])
                ],
                'required'=>true
            ])

            ->add('sigle', TextType::class, [
                'label'=>'Sigle',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
                ]
            ])

            ->add('personne_ressource', TextType::class, [
                'label'=>'Personne ressource',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
                ]
            ])

            ->add('cc_usine', TextType::class, [
                'label'=>'Compte contribuable',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
                ]
            ])

            ->add('tel_usine', TextType::class, [
                'label'=>'Numéro Téléphone',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
                ]
            ])
            ->add('fax_usine', TextType::class, [
                'label'=>'Fax',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
                ]
            ])
            ->add('adresse_usine', TextType::class, [
                'label'=>'Adresse',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
                ]
            ])

            ->add('localisation_usine', TextType::class, [
                'label'=>'Localisation',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
                ]
            ])
            ->add('ville', TextType::class, [
                'label'=>'Ville',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
                ]
            ])
            ->add('capacite_usine', NumberType::class, [
                'label'=>'Capacité',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
                ]
            ])


            ->add('email_personne_ressource', TextType::class, [
                'label'=>'Email Personne ressource',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
                ]
            ])
            ->add('mobile_personne_ressource', TextType::class, [
                'label'=>'Mobile Perssone ressource',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
                ]
            ])

            ->add('export', CheckboxType::class, [
                'label'=>'Cette usine est-elle exportatrice de bois ?',
                'label_attr'=>[
                    'class'=>'checkbox fw-bold text-dark'
                ]
            ])

            ->add('code_exportateur', TextType::class, [
                'label'=>'Code Exportateur',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
                ]
            ])

            ->add('type_transformation', EntityType::class, [
                'label'=>'Type de transformation',
                'class'=> TypeTransformation::class,
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'choices form-select multiple-remove'
                ],
                'multiple'=>true,
                'expanded'=>false
            ])

            ->add('code_cantonnement', EntityType::class, [
                'label'=>'Sélectionner le cantonnement',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'placeholder'=>'Sélectionner le cantonnement',
                'class'=> Cantonnement::class,

                'multiple'=>false,
                'expanded'=>false,
                'attr'=>[
                    ' class'=>'form-control code_cantonnement alert-light text-dark',
                    'placeholder'=>'--Sélectionnez le cantonnement forestier...'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usine::class,
        ]);
    }
}
