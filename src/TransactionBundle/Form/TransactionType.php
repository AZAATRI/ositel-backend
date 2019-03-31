<?php

namespace TransactionBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title',TextType::class)
            ->add('amount',NumberType::class)
            ->add('isInput')
            ->add('description',TextareaType::class,array('required'=>false))
            ->add('isValid')
            ->add('category',EntityType::class, array(
                'class' => 'TransactionBundle\Entity\Category',
                'choice_value' => 'id',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c');
                }))
            ->add('tags',EntityType::class, array(
                    'class' => 'TransactionBundle\Entity\Tag',
                    'choice_value' => 'id',
                    'multiple' => true
                )
            )
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TransactionBundle\Entity\Transaction'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'transactionbundle_transaction';
    }


}
