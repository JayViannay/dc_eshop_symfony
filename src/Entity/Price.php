<?php

namespace App\Entity;

use App\Repository\PriceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceRepository::class)]
class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\OneToMany(mappedBy: 'price', targetEntity: Reference::class)]
    private Collection $refs;

    public function __construct()
    {
        $this->refs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return Collection<int, Reference>
     */
    public function getRefs(): Collection
    {
        return $this->refs;
    }

    public function addRef(Reference $ref): self
    {
        if (!$this->refs->contains($ref)) {
            $this->refs->add($ref);
            $ref->setPrice($this);
        }

        return $this;
    }

    public function removeRef(Reference $ref): self
    {
        if ($this->refs->removeElement($ref)) {
            // set the owning side to null (unless already changed)
            if ($ref->getPrice() === $this) {
                $ref->setPrice(null);
            }
        }

        return $this;
    }
}
