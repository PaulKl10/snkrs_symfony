<?php

namespace App\Entity;

use App\Repository\NftRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NftRepository::class)]
class Nft
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['nft:read', 'category:read', 'user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'category:read', 'nft:read', 'purchaseNft:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['nft:read', 'category:read', 'user:read'])]
    private ?string $img = null;

    #[ORM\Column]
    #[Groups(['nft:read', 'category:read'])]
    private ?int $stock = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['nft:read', 'user:read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['nft:read', 'user:read'])]
    private ?\DateTimeInterface $launch_date = null;

    #[ORM\ManyToOne(inversedBy: 'nfts')]
    #[ORM\JoinColumn(name: "category_id", nullable: false, referencedColumnName: "id")]
    #[Groups(['nft:read', 'user:read'])]
    private ?Category $Category = null;

    #[ORM\OneToOne(mappedBy: 'Nft', cascade: ['persist', 'remove'])]
    #[Groups(['nft:read', 'category:read', 'user:read'])]
    private ?NftPrice $nftPrice = null;

    #[ORM\OneToMany(mappedBy: 'Nft', targetEntity: PurchaseNft::class, orphanRemoval: true)]
    #[Groups(['nft:read'])]
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
