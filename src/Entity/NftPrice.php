<?php

namespace App\Entity;

use App\Repository\NftPriceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NftPriceRepository::class)]
class NftPrice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $price_date = null;

    #[ORM\Column]
    private ?float $price_eth_value = null;

    #[ORM\OneToOne(inversedBy: 'nftPrice', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Nft $Nft = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriceDate(): ?\DateTimeInterface
    {
        return $this->price_date;
    }

    public function setPriceDate(\DateTimeInterface $price_date): static
    {
        $this->price_date = $price_date;

        return $this;
    }

    public function getPriceEthValue(): ?float
    {
        return $this->price_eth_value;
    }

    public function setPriceEthValue(float $price_eth_value): static
    {
        $this->price_eth_value = $price_eth_value;

        return $this;
    }

    public function getNft(): ?Nft
    {
        return $this->Nft;
    }

    public function setNft(Nft $Nft): static
    {
        $this->Nft = $Nft;

        return $this;
    }
}
