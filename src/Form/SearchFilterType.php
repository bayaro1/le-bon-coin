<?php

namespace App\Form;

use App\Entity\Category;
use App\DataModel\SearchFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('qSearch', TextType::class, [
                'required' => false
            ])
            ->add('city', TextType::class, [
                'required' => false
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'choice_value' => 'name', 
                'required' => false
            ])

            ->add('sort', ChoiceType::class, [
                'required' => false,
                'label' => false,
                'choices' => [
                    'Tri : Plus récentes' => SearchFilter::DATE_DESC,
                    'Tri : Plus anciennes' => SearchFilter::DATE_ASC,
                    'Tri : Prix croissants' => SearchFilter::PRICE_ASC,
                    'Tri : Prix décroissants' => SearchFilter::PRICE_DESC
                ],
                'placeholder' => 'Tri : Pertinence'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchFilter::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }
    public function getBlockPrefix()
    {
        return '';
    }
}
