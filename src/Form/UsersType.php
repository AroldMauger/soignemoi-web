<?php
namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;

class UsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'L\'email ne peut pas être vide.',
                    ]),
                    new Assert\Email([
                        'message' => 'L\'email {{ value }} n\'est pas un email valide.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/i',
                        'message' => 'L\'email doit être au format valide (ex: utilisateur@service.com)',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le mot de passe ne peut pas être vide.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&+=!])(?!.*\s).{8,}$/',
                        'message' => 'Le mot de passe doit contenir au moins une minuscule, une majuscule, un chiffre, un caractère spécial, et doit avoir au moins 8 caractères.',
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le prénom ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'L\'adresse ne peut pas être vide.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
            'csrf_protection' => true,
        ]);
    }
}

