<?php

namespace App\Form\References;

use App\Entity\References\Cantonnement;
use App\Entity\References\TypeForet;
use App\Entity\References\Foret;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ForetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero_foret', NumberType::class, [
                'label'=>'N° de la foret',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-primary'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le numéro de la forêt est obligatoire',
                    ])
                ],
                'required'=>true
            ])

            ->add('denomination', TextType::class, [
                'label'=>'Nom (vernaculaire) de la foret',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-primary'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseignez le nom de la forêt',
                    ])
                ],
                'required'=>true
            ])

            ->add('superficie', NumberType::class, [
                'label'=>'Superficie',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
                ]
            ])

            ->add('date_premiere_attribution', DateType::class, [
                'label'=>'Date première attribution',
                'widget'=>'single_text',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    '           class'=>'form-control alert-light text-dark'
                ]
            ])

            ->add('code_type_foret', EntityType::class, [
                'label'=>'Type de forêt',
                'class'=> TypeForet::class,
                'placeholder'=>'--Sélectionnez le type de forêts...',
                'label_attr'=>[
                    'class'=>'fw-bold text-dark'
                ],
                'attr'=>[
                    'class'=>'form-control alert-light text-dark type_foret',
                    'placeholder'=>'--Sélectionnez le type de forêts...'
                ],

                'multiple'=>false,
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
            'data_class' => Foret::class,
        ]);
    }
}
