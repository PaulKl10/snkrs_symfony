<?php

namespace App\Form;

use App\Entity\PurchaseNft;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseNftType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('purchase_date')
            ->add('nft_eth_price')
            ->add('nft_eur_price')
            ->add('User')
            ->add('Nft')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchaseNft::class,
        ]);
    }
}
