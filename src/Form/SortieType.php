<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Exemple : Lorem Ipsun',
                ]
            ])
            ->add('dateHeureDebut', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Duree (heure)',
                'attr' => [
                    'placeholder' => 'Exemple : 24 (heures)',
                ]
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('nbInscriptionMax')
            ->add('infosSortie')
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'placeholder' => 'Choisissez une ville',
                'choice_label' => 'nom',
                'mapped' => false,
                'query_builder' => function (VilleRepository $villeRepository) {
                return $villeRepository->createQueryBuilder('v');
                }
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'placeholder' => 'Choisissez un lieu',
                'choice_label' => 'nom',
            ])

            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
            ]);
//           ->add('submit', SubmitType::class, [
//               'label' => 'Créer',
//           ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
