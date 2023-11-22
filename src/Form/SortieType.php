<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom :',
                'attr' => [
                    'autofocus' => 'autofocus'
                ]
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie :',
                'html5' => true,
                'widget' => 'single_text',
                'empty_data' => ' '//bon fonctionnement requetes ajax
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => "Date limite d'inscription :",
                'html5' => true,
                'widget' => 'single_text',
                'empty_data' => ' '
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre maximal de participants :'
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée :'
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos :'
            ])
            ->add('campus', TextType::class, [
                'disabled' => true,
                'label' => 'Campus :'
            ])
            ->add('ville', EntityType::class, [
                'mapped' => false,
                'class' => Ville::class,
                'choice_label' => 'nom',
                'label' => 'Ville :',
                'placeholder' => 'Choisissez une ville',
                'query_builder'=> function( VilleRepository $villeRepository ) {
                    return $villeRepository->createQueryBuilder('c')->orderBy('c.nom', 'ASC');
                }
            ])
            ->add('lieu', ChoiceType::class, [
                'placeholder' => "Veuillez choisir une ville",
                'label' => 'Lieux :'
            ])
            ->add('rue', TextType::class, [
                'mapped' => false,
                'label' => 'Rue :',
                'disabled' => true
            ])
            ->add('codePostal', TextType::class, [
                'mapped' => false,
                'label' => 'Code Postal :',
                'disabled' => true
            ])
            ->add('latitude', TextType::class, [
                'mapped' => false,
                'label' => 'Rue :',
                'disabled' => true
            ])
            ->add('longitude', TextType::class, [
                'mapped' => false,
                'label' => 'Rue :',
                'disabled' => true
            ])
            ->add('enregistrer', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-success',
                    'style' => 'width: 180px',
                    'formnovalidate' => 'formnovalidate'
                ]
            ])
            ->add('publier', SubmitType::class, [
                'label' => 'Publier',
                'attr' => [
                    'class' => 'btn btn-success',
                    'style' => 'width: 180px',
                    'formnovalidate' => 'formnovalidate'
                ],
            ])
        ;

        //**RECUPERATION DE LA LISTE DES LIEUX CORRESPONDANT A LA VILLE CHOISIE****

        $formModifier = function(FormInterface $form, Ville $ville = null) {
            $lieu = ( $ville === null ) ? [] : $ville->getLieux();
            $form->add( 'lieu', EntityType::class, [
                'class' => Lieu::class,
                'choices' => $lieu,
                'choice_label' => 'nom',
                'placeholder' => "Veuillez choisir un lieu",
                'label' => 'Lieux :'
            ]);
        };

        $builder->get('ville')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) use ($formModifier) {
                $ville = $event->getForm()->getData();
                $formModifier( $event->getForm()->getParent(), $ville );
            }
        );

        //****************************************************************************

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
