<?php

namespace App\Form;

use App\Entity\Playlist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaylistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'label'=> 'Titre de la playlist :' ,
                'constraints'=>[
                    new Length([
                        'min' => 1,
                        'maxMessage' => 'Le titre de la playlist peux contenir au maximum {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 20,
                    ]),
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
