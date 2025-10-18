<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName',TextType::class, ['label'=>'Enter Your Full Name','required'=>false])
            ->add('phone', TextType::class, ['label'=>'Enter Your Phone Number'])
            ->add('address', TextType::class, ['label'=>'Enter Your Address','required'=>false])
            ->add('zipcode', IntegerType::class, ['label'=>'Enter Your Zipcode','required'=>false])
            ->add('isDeleted', CheckboxType::class, ['label'=>'Is Deleted','required'=>false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
