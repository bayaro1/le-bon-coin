<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\SearchFilter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('GET')
            ->add('qSearch')
            ->add('city')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name', 
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchFilter::class,
        ]);
    }
}
