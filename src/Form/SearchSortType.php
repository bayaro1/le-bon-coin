<?php

namespace App\Form;

use App\DataModel\SearchSort;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchSortType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sort', ChoiceType::class, [
                'required' => false,
                'label' => false,
                'choices' => [
                    'Tri : Plus récentes' => SearchSort::DATE_DESC,
                    'Tri : Plus anciennes' => SearchSort::DATE_ASC,
                    'Tri : Prix croissants' => SearchSort::PRICE_ASC,
                    'Tri : Prix décroissants' => SearchSort::PRICE_DESC
                ],
                'placeholder' => 'Tri : Pertinence'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchSort::class,
        ]);
    }
}
