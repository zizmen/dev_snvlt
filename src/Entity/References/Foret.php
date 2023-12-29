<?php

namespace App\Entity\References;

use App\Entity\Administration\InventaireForestier;
use App\Entity\Autorisation\Attribution;
use App\Repository\References\ForetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'metier.foret')]
#[ORM\Entity(repositoryClass: ForetRepository::class)]
#[UniqueEntity(fields: ['numero_foret'], message: 'The forest numberr code already exists')]
class Foret
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_foret = null;

    #[ORM\Column(length: 255)]
    private ?string $denomination = null;

    #[ORM\Column(nullable: true)]
    private ?float $superficie = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_premiere_attribution = null;

    #[ORM\ManyToOne(inversedBy: 'forets')]
    private ?TypeForet $code_type_foret = null;

    #[ORM\ManyToOne(inversedBy: 'forets')]
    private ?Cantonnement $code_cantonnement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\OneToMany(mappedBy: 'code_foret', targetEntity: Attribution::class)]
    private Collection $attributions;

    #[ORM\Column(nullable: true)]
    private ?bool $attribue = null;

    #[ORM\Column(nullable: true)]
    private ?bool $reprise = null;

    #[ORM\OneToMany(mappedBy: 'code_foret', targetEntity: InventaireForestier::class)]
    private Collection $inventaireForestiers;

    public function __construct()
    {
        $this->attributions = new ArrayCollection();
        $this->inventaireForestiers = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroForet(): ?string
    {
        return $this->numero_foret;
    }

    public function setNumeroForet(string $numero_foret): static
    {
        $this->numero_foret = $numero_foret;

        return $this;
    }

    public function getDenomination(): ?string
    {
        return $this->denomination;
    }

    public function setDenomination(string $denomination): static
    {
        $this->denomination = $denomination;

        return $this;
    }

    public function getSuperficie(): ?float
    {
        return $this->superficie;
    }

    public function setSuperficie(?float $superficie): static
    {
        $this->superficie = $superficie;

        return $this;
    }

    public function getDatePremiereAttribution(): ?\DateTimeInterface
    {
        return $this->date_premiere_attribution;
    }

    public function setDatePremiereAttribution(?\DateTimeInterface $date_premiere_attribution): static
    {
        $this->date_premiere_attribution = $date_premiere_attribution;

        return $this;
    }

    public function getCodeTypeForet(): ?TypeForet
    {
        return $this->code_type_foret;
    }

    public function setCodeTypeForet(?TypeForet $code_type_foret): static
    {
        $this->code_type_foret = $code_type_foret;

        return $this;
    }

    public function getCodeCantonnement(): ?Cantonnement
    {
        return $this->code_cantonnement;
    }

    public function setCodeCantonnement(?Cantonnement $code_cantonnement): static
    {
        $this->code_cantonnement = $code_cantonnement;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    public function setUpdatedBy(?string $updated_by): static
    {
        $this->updated_by = $updated_by;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function __toString(): string
    {
        return $this->denomination;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @param \DateTimeImmutable|null $created_at
     */
    public function setCreatedAt(?\DateTimeImmutable $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return string|null
     */
    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    /**
     * @param string|null $created_by
     */
    public function setCreatedBy(?string $created_by): void
    {
        $this->created_by = $created_by;
    }

    /**
     * @return Collection<int, Attribution>
     */
    public function getAttributions(): Collection
    {
        return $this->attributions;
    }

    public function addAttribution(Attribution $attribution): static
    {
        if (!$this->attributions->contains($attribution)) {
            $this->attributions->add($attribution);
            $attribution->setCodeForet($this);
        }

        return $this;
    }

    public function removeAttribution(Attribution $attribution): static
    {
        if ($this->attributions->removeElement($attribution)) {
            // set the owning side to null (unless already changed)
            if ($attribution->getCodeForet() === $this) {
                $attribution->setCodeForet(null);
            }
        }

        return $this;
    }

    public function isAttribue(): ?bool
    {
        return $this->attribue;
    }

    public function setAttribue(?bool $attribue): static
    {
        $this->attribue = $attribue;

        return $this;
    }

    public function isReprise(): ?bool
    {
        return $this->reprise;
    }

    public function setReprise(?bool $reprise): static
    {
        $this->reprise = $reprise;

        return $this;
    }

    /**
     * @return Collection<int, InventaireForestier>
     */
    public function getInventaireForestiers(): Collection
    {
        return $this->inventaireForestiers;
    }

    public function addInventaireForestier(InventaireForestier $inventaireForestier): static
    {
        if (!$this->inventaireForestiers->contains($inventaireForestier)) {
            $this->inventaireForestiers->add($inventaireForestier);
            $inventaireForestier->setCodeForet($this);
        }

        return $this;
    }

    public function removeInventaireForestier(InventaireForestier $inventaireForestier): static
    {
        if ($this->inventaireForestiers->removeElement($inventaireForestier)) {
            // set the owning side to null (unless already changed)
            if ($inventaireForestier->getCodeForet() === $this) {
                $inventaireForestier->setCodeForet(null);
            }
        }

        return $this;
    }


}
