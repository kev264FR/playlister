<?php

namespace App\Form;

use App\Entity\Playlist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PlaylistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label'=> 'Titre de la playlist :' ,
                'constraints'=>[
                    new Length([
                        'maxMessage' => 'Le titre de la playlist peut contenir au maximum {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 20,
                    ]),
                    new NotBlank([
                        'message'=> 'Ce champ est obligatoire'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Playlist::class,
        ]);
    }
}
