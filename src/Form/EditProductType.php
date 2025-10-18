<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title (from class)',
                'required' => true,
            ])
            ->add('price', NumberType::class, [
                'label' => 'Price',
                'scale' => 2,
                'required' => true,
                'html5' => true,
                'attr' => [
                    'step' => '0.01',
                ]
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantity',
                'scale' => 2,
                'required' => true,
                'html5' => true,
            ])
            ->add('description')
            ->add('is_published', CheckboxType::class, [
                'label' => 'Is published',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
