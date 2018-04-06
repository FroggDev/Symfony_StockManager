<?php
namespace App\Form\Security;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserType
 */
class UserType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'firstname',
            TextType::class,
            [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Set your firstname.'
                ]
            ]
        )->add(
            'lastname',
            TextType::class,
            [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Set your lastname.'
                ]
            ]
        )->add(
            'email',
            EmailType::class,
            [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Set your email.'
                ]
            ]
        )->add(
            'password',
            PasswordType::class,
            [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Set your password.'
                ]
            ]
        )->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Create account',
                'attr' => array('class' => 'btn btn-block btn-primary')
            ]
        )->getForm();
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
