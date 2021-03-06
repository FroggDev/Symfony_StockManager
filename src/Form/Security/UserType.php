<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Security;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Frogg <admin@frogg.fr>
 */
class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param null|array           $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options = null)
    {
        $builder
            ->add('firstname', TextType::class, ['attr' => ['class' => 'validate']])
            ->add('lastname', TextType::class, ['attr' => ['class' => 'validate']])
            ->add('email', EmailType::class, ['attr' => ['class' => 'validate']])
            ->add('password', TextType::class, ['attr' => ['class' => 'validate']]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
