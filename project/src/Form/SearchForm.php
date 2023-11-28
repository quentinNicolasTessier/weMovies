<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('searchText', SearchType::class, ['label' => 'input_search', 'attr' => array(
                'placeholder' => 'Search movies...'
            )])
            ->add('search', SubmitType::class, ['label' => 'search']);
    }
}