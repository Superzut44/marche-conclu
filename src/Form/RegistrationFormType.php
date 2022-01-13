<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'label' => 'Adresse mail',
        ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => 'form.register.password.label',
                'label_attr' => ['class' => 'text-blue'],
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 'id' => 'registration_form_email'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => "Votre mot de passe doit être long d'au moins {{ limit }} caractères",
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'invalid_message' => 'Erreur avec le mot de passe',
                'first_options' => [
                    'attr' => ['placeholder' => '',
                    'class' => 'form-control'],
                    'label' => 'Mot de passe'
                ],
                'second_options' => [
                    'attr' => ['placeholder' => '',
                    'class' => 'mt-1 form-control'],
                    'label' => 'Confirmer votre mot de passe'
                ]
            ])
            ->add('firstname', TextType::class, ['label' => 'Prénom'])
            ->add('lastname', TextType::class, ['label' => 'Nom de famille'])
            ->add('phone_number', TextType::class, ['label' => 'Numéro de téléphone'])
            ->add('job', TextType::class, ['label' => 'Profession'])
            ->add('avatar', TextType::class, ['label' => 'Photo de profil', 'required' => false])
            ->add('company', TextType::class, ['label' => 'Nom de votre société', 'required' => false])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('enregistrer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
