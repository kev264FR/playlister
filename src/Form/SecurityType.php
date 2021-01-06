<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class SecurityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('email', null, [
                'constraints' => [
                    new NotBlank([
                        'message'=> 'Ce champ est obligatoire'
                    ])
                ]
            ])
            
            ->add('password', PasswordType::class, [
                'mapped'=>false,
                'label'=>'Mot de passe actuel :',
                'constraints' => [
                    new NotBlank([
                        'message'=> 'Ce champ est obligatoire'
                    ])
                ]
            ])

            ->add('validation', CheckboxType::class, [
                'label'=>'Continuer et supprimer mon compte',
                'required'=>true,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
