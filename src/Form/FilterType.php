<?php

namespace App\Form;

use App\Entity\Campus;
use App\Repository\CampusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                "required" => false,
                'class' => Campus::class,
                'choice_label' => 'nom',
                'query_builder' => function (CampusRepository $campusRepository) {
                    return $campusRepository->createQueryBuilder('c');
                },
                'placeholder' => 'Tous les campus',
            ])
            ->add('nom', TextType::class, [
                "required" => false,
            ])
            ->add('dateIntervalDebut', DateType::class, [
                "required" => false,
                'label' =>  false,
                'widget' => 'single_text',
            ])
            ->add('dateIntervalFin', DateType::class, [
                "required" => false,
                'label' =>  false,
                'widget' => 'single_text',
            ])
//            ->add('filters', ChoiceType::class, [
//                "required" => false,
//                'label' => false,
//                'expanded' => true,
//                'multiple' => true,
//                'choices' => [
//                    "Sorties dont je suis l'organisateur/trice" => 'ORGA',
//                    'Sorties auxquelles je suis inscrit/e' => 'NOT_INSC',
//                    'Sorties auxquelles je ne suis pas inscrit/e' => 'INSC',
//                    'Sorties passées' => 'PASSEE',
//                ]
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
