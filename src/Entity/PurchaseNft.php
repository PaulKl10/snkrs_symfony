<?php

namespace App\Entity;

use App\Repository\PurchaseNftRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PurchaseNftRepository::class)]
class PurchaseNft
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['purchaseNft:read', 'user:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseNfts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['nft:read', 'purchaseNft:read'])]
    private ?User $User = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseNfts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:read', 'purchaseNft:read'])]
    private ?Nft $Nft = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['user:read', 'nft:read', 'purchaseNft:read'])]
    private ?\DateTimeInterface $purchase_date = null;

    #[ORM\Column]
    #[Groups(['user:read', 'nft:read', 'purchaseNft:read'])]
    private ?float $nft_eth_price = null;

    #[ORM\Column]
    #[Groups(['user:read', 'nft:read', 'purchaseNft:read'])]
    private ?float $nft_eur_price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function getNft(): ?Nft
    {
        return $this->Nft;
    }

    public function setNft(?Nft $Nft): static
    {
        $this->Nft = $Nft;

        return $this;
    }

    public function getPurchaseDate(): ?\DateTimeInterface
    {
        return $this->purchase_date;
    }

    public function setPurchaseDate(\DateTimeInterface $purchase_date): static
    {
        $this->purchase_date = $purchase_date;

        return $this;
    }

    public function getNftEthPrice(): ?float
    {
        return $this->nft_eth_price;
    }

    public function setNftEthPrice(float $nft_eth_price): static
    {
        $this->nft_eth_price = $nft_eth_price;

        return $this;
    }

    public function getNftEurPrice(): ?float
    {
        return $this->nft_eur_price;
    }

    public function setNftEurPrice(float $nft_eur_price): static
    {
        $this->nft_eur_price = $nft_eur_price;

        return $this;
    }
}
