<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Utilisateur;
use Container7xAvKtY\getCampusRepositoryService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class GererMonProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class,[
                 'label'=> 'Pseudo :'
            ])
            ->add('prenom', TextType::class,[
                'label'=> 'Prenom :'
            ])
            ->add('nom', TextType::class,[
                'label' => 'Nom :'

            ])
            ->add('telephone', TelType::class,[
                'label' => 'Telephone :'
            ])
            ->add('email', EmailType::class,[
                'label' => 'Email :'
            ])
           /* ->add('password', RepeatedType::class,[
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'first_options'  => ['label' => 'Mot de passe :'],
                'second_options' => ['label' => 'Confirmation :'],
            ])*/


            ->add('campus', EntityType::class, [
                'label' => 'Campus :',
                'class'=> Campus::class,
                'choice_label'=>'nom'
            ])
           /* ->add('photo', FileType::class,[
               'mapped'=>false,
                'constraints' => [
                      new Image(['maxSize' => '1024k'])
                    ],
           ])*/




        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => Utilisateur::class,
        ]);
    }
}
