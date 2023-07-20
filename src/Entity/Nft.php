<?php

namespace App\Entity;

use App\Repository\NftRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NftRepository::class)]
class Nft
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $img = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $launch_date = null;

    #[ORM\ManyToOne(inversedBy: 'nfts')]
    #[ORM\JoinColumn(name: "category_id", nullable: false, referencedColumnName: "id")]
    private ?Category $Category = null;

    #[ORM\OneToOne(mappedBy: 'Nft', cascade: ['persist', 'remove'])]
    private ?NftPrice $nftPrice = null;

    #[ORM\OneToMany(mappedBy: 'Nft', targetEntity: PurchaseNft::class, orphanRemoval: true)]
    private Collection $purchaseNfts;

    public function __construct()
    {
        $this->purchaseNfts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): static
    {
        $this->img = $img;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLaunchDate(): ?\DateTimeInterface
    {
        return $this->launch_date;
    }

    public function setLaunchDate(\DateTimeInterface $launch_date): static
    {
        $this->launch_date = $launch_date;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->Category;
    }

    public function setCategory(?Category $Category): static
    {
        $this->Category = $Category;

        return $this;
    }

    public function getNftPrice(): ?NftPrice
    {
        return $this->nftPrice;
    }

    public function setNftPrice(NftPrice $nftPrice): static
    {
        // set the owning side of the relation if necessary
        if ($nftPrice->getNft() !== $this) {
            $nftPrice->setNft($this);
        }

        $this->nftPrice = $nftPrice;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseNft>
     */
    public function getPurchaseNfts(): Collection
    {
        return $this->purchaseNfts;
    }

    public function addPurchaseNft(PurchaseNft $purchaseNft): static
    {
        if (!$this->purchaseNfts->contains($purchaseNft)) {
            $this->purchaseNfts->add($purchaseNft);
            $purchaseNft->setNft($this);
        }

        return $this;
    }

    public function removePurchaseNft(PurchaseNft $purchaseNft): static
    {
        if ($this->purchaseNfts->removeElement($purchaseNft)) {
            // set the owning side to null (unless already changed)
            if ($purchaseNft->getNft() === $this) {
                $purchaseNft->setNft(null);
            }
        }

        return $this;
    }
}
