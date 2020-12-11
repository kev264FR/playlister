<?php

namespace App\Form;

use App\Entity\Platform;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlatformType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,[
                'constraints' => [
                    new NotBlank([
                        'message'=> 'Ce champ est obligatoire'
                    ])
                ]
            ])
            ->add('baseUrl', null, [
                'constraints' => [
                    new NotBlank([
                        'message'=> 'Ce champ est obligatoire'
                    ])
                ]
            ])
            ->add('targetUrl', null, [
                'constraints' => [
                    new NotBlank([
                        'message'=> 'Ce champ est obligatoire'
                    ])
                ]
            ])
            ->add('imgUrl', null, [
                'constraints' => [
                    new NotBlank([
                        'message'=> 'Ce champ est obligatoire'
                    ])
                ]
            ])
            ->add('api')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Platform::class,
        ]);
    }
}
