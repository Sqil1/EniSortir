<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;

use Doctrine\DBAL\Types\DateImmutableType;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Finder\Iterator\DateRangeFilterIterator;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;

class ListeSortiesType extends AbstractType
{
//    public function buildForm(FormBuilderInterface $builder, array $options): void
//    {
//        $builder
//            ->add('nom', TextType::class, [
//                'required' => false,
//            ])
//
//            ->add('campus', EntityType::class, [
//                'class' => Campus::class,
//                'choice_label' => 'nom',
//                'placeholder' => 'Sélectionnez un campus',
//                'required' => false,
//            ])
//            ->add('dateHeureDebut', DateTimeType::class, [
//                'label' => 'Date',
//                'widget' => 'single_text',
//                'required' => false,
//                'data' => new \DateTime('now')
//            ])
//            ->add('isOrganisateur', CheckboxType::class, [
//                'label' => 'Organisateur',
//                'required' => false,
//            ]);
//;
//    }
//
//    public function configureOptions(OptionsResolver $resolver): void
//    {
//        $resolver->setDefaults([
//            'data_class' => Sortie::class,
//        ]);
//    }
}
