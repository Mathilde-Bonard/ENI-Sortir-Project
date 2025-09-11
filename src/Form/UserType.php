<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo')
            ->add('prenom')
            ->add('nom')
            ->add('telephone')
            ->add('email')
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
            ])
            ->add('photo', FileType::class, [
                'mapped' => false,
                'required' => false,

                'constraints' => [
                    new Image(['maxSize' => '5000k'])
                ]
            ])
            // Bouton submit qui apparaitra directement dans twig avec le form_widget
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'transition w-full font-semibold text-neutral-800 border-2 bg-yellow-100 pt-2 pb-1.5 mt-1 rounded-full text-smg'],
                'label' => $options['submit_label'] ?? 'Modifier'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'submit_label' => 'Créer', // label par défaut
        ]);
    }
}
