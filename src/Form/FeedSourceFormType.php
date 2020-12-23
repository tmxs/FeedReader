<?php

namespace App\Form;

use App\Entity\FeedCategory;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class FeedSourceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', TextType::class)
            ->add('title', TextType::class)
            // ->add('category', TextType::class)
            ->add('category', EntityType::class, [
                'class' => FeedCategory::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
            ]);
    }
}
