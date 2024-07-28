<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            // ->add('roles')
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('prenom')
            ->add('nom')
            ->add('genre',  EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'genre',
                'attr' => ['class' => 'form-control genre-field']
            ])
            ->add('photo', FileType::class, [
                'mapped'=>false,
                'label'=>' ',
                'required' => false,
                'attr' => [
                    'id' => 'photo',
                    'size' => '20000000',
                    'accept'=>'.jpg,.png,.svg,.webp.avif,.gif,',
                    'name' => 'photo',
                    'type' => 'file'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
