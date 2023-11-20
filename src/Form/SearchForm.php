<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\Campus;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchForm extends AbstractType
{
    //construction du formulaire
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('s', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher'
                ]
            ])
            ->add('campus', EntityType::class, [
                'choice_label' => 'nom',
                'label' => false,
                'required' => false,
                'class' => Campus::class,
                'expanded' => false,
                'multiple' => false
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Entre'
                ],
                'widget' => 'single_text',
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'de'
                ],
                'widget' => 'single_text',
            ])
            ->add('isOrganisateur', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur/trice',
                'required' => false,
            ])
            ->add('isInscrit', CheckboxType::class, [
                'label' => 'Sorties auquelles je suis inscrit/e',
                'required' => false,
            ])
            ->add('isNotInscrit', CheckboxType::class, [
                'label' => 'Sorties auquelles je suis ne suis pas inscrit/e',
                'required' => false,
            ])
            ->add('isTermine', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false,
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        //valeurs par defaut
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }
    public function getBlockPrefix()
    {
        return '';
    }
}