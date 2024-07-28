<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
         
        ->add('nom', TextType::class, [
            'attr' => ['class' => 'form-style'],
            
        ])
        ->add('prenom', TextType::class, [
            'attr' => ['class' => 'form-style'],
            
        ])
        ->add('genre',  EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'genre'    
            ])
        ->add('email')
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
        ])
        ->add('agreeTerms', CheckboxType::class, [
                 'mapped' => false,
                 'constraints' => [
                new IsTrue([
                    'message' => 'Vous devez accepter nos conditions.',
                    ]),
                ],
            ])
        ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
