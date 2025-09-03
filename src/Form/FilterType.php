<?php

namespace App\Form;

use App\Entity\Campus;
use App\Repository\CampusRepository;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'query_builder' => function (CampusRepository $campusRepository) {
                    return $campusRepository->createQueryBuilder('c');
                }
            ])
            ->add('nom')
            ->add('dateIntervalDebut', DateType::class, [
                'label' =>  false,
                'widget' => 'single_text',
            ])
            ->add('dateIntervalFin', DateType::class, [
                'label' =>  false,
                'widget' => 'single_text',
            ])
            ->add('genres', ChoiceType::class, [
                'label' => false,
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    "Sorties dont je suis l'organisateur/trice" => 'drama',
                    'Sorties auxquelles je suis inscrit/e' => 'sf',
                    'Sorties auxquelles je ne suis pas inscrit/e' => 'horror',
                    'Sorties passées' => 'comedy',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
