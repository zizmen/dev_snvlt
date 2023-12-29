<?php

namespace App\Entity\References;

use App\Entity\Administration\InventaireForestier;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\DocStats\Saisie\Lignepagebtgu;
use App\Entity\DocStats\Saisie\Lignepagecp;
use App\Entity\DocStats\Saisie\Lignepagelje;
use App\Repository\References\EssenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'metier.essence')]
#[ORM\Entity(repositoryClass: EssenceRepository::class)]
#[UniqueEntity(fields: ['nom_vernaculaire'], message: 'There is already a name with this forest species')]
class Essence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 4)]
    private ?string $numero_essence = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_vernaculaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $famille_essence = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom_scientifique = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $categorie_essence = null;

    #[ORM\Column(nullable: true)]
    private ?int $taxe_abattage = null;

    #[ORM\Column]
    private ?int $dm_minima = null;

    #[ORM\Column(nullable: true)]
    private ?int $taxe_preservation = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'code_essence', targetEntity: InventaireForestier::class)]
    private Collection $inventaireForestiers;

    #[ORM\OneToMany(mappedBy: 'nom_essencecp', targetEntity: Lignepagecp::class)]
    private Collection $lignepagecps;

    #[ORM\OneToMany(mappedBy: 'nom_essencebrh', targetEntity: Lignepagebrh::class)]
    private Collection $lignepagebrhs;

    #[ORM\OneToMany(mappedBy: 'essence', targetEntity: Lignepagelje::class)]
    private Collection $lignepageljes;

    #[ORM\OneToMany(mappedBy: 'essence', targetEntity: Lignepagebtgu::class)]
    private Collection $lignepagebtgus;

    public function __construct()
    {
        $this->inventaireForestiers = new ArrayCollection();
        $this->lignepagecps = new ArrayCollection();
        $this->lignepagebrhs = new ArrayCollection();
        $this->lignepageljes = new ArrayCollection();
        $this->lignepagebtgus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroEssence(): ?string
    {
        return $this->numero_essence;
    }

    public function setNumeroEssence(string $numero_essence): static
    {
        $this->numero_essence = $numero_essence;

        return $this;
    }

    public function getNomVernaculaire(): ?string
    {
        return $this->nom_vernaculaire;
    }

    public function setNomVernaculaire(string $nom_vernaculaire): static
    {
        $this->nom_vernaculaire = $nom_vernaculaire;

        return $this;
    }

    public function getFamilleEssence(): ?string
    {
        return $this->famille_essence;
    }

    public function setFamilleEssence(?string $famille_essence): static
    {
        $this->famille_essence = $famille_essence;

        return $this;
    }

    public function getNomScientifique(): ?string
    {
        return $this->nom_scientifique;
    }

    public function setNomScientifique(?string $nom_scientifique): static
    {
        $this->nom_scientifique = $nom_scientifique;

        return $this;
    }

    public function getCategorieEssence(): ?string
    {
        return $this->categorie_essence;
    }

    public function setCategorieEssence(?string $categorie_essence): static
    {
        $this->categorie_essence = $categorie_essence;

        return $this;
    }

    public function getTaxeAbattage(): ?int
    {
        return $this->taxe_abattage;
    }

    public function setTaxeAbattage(?int $taxe_abattage): static
    {
        $this->taxe_abattage = $taxe_abattage;

        return $this;
    }

    public function getDmMinima(): ?int
    {
        return $this->dm_minima;
    }

    public function setDmMinima(int $dm_minima): static
    {
        $this->dm_minima = $dm_minima;

        return $this;
    }

    public function getTaxePreservation(): ?int
    {
        return $this->taxe_preservation;
    }

    public function setTaxePreservation(?int $taxe_preservation): static
    {
        $this->taxe_preservation = $taxe_preservation;

        return $this;
    }
    public function __toString(): string
    {
        return $this->nom_vernaculaire;
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
            $inventaireForestier->setCodeEssence($this);
        }

        return $this;
    }

    public function removeInventaireForestier(InventaireForestier $inventaireForestier): static
    {
        if ($this->inventaireForestiers->removeElement($inventaireForestier)) {
            // set the owning side to null (unless already changed)
            if ($inventaireForestier->getCodeEssence() === $this) {
                $inventaireForestier->setCodeEssence(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lignepagecp>
     */
    public function getLignepagecps(): Collection
    {
        return $this->lignepagecps;
    }

    public function addLignepagecp(Lignepagecp $lignepagecp): static
    {
        if (!$this->lignepagecps->contains($lignepagecp)) {
            $this->lignepagecps->add($lignepagecp);
            $lignepagecp->setNomEssencecp($this);
        }

        return $this;
    }

    public function removeLignepagecp(Lignepagecp $lignepagecp): static
    {
        if ($this->lignepagecps->removeElement($lignepagecp)) {
            // set the owning side to null (unless already changed)
            if ($lignepagecp->getNomEssencecp() === $this) {
                $lignepagecp->setNomEssencecp(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lignepagebrh>
     */
    public function getLignepagebrhs(): Collection
    {
        return $this->lignepagebrhs;
    }

    public function addLignepagebrh(Lignepagebrh $lignepagebrh): static
    {
        if (!$this->lignepagebrhs->contains($lignepagebrh)) {
            $this->lignepagebrhs->add($lignepagebrh);
            $lignepagebrh->setNomEssencebrh($this);
        }

        return $this;
    }

    public function removeLignepagebrh(Lignepagebrh $lignepagebrh): static
    {
        if ($this->lignepagebrhs->removeElement($lignepagebrh)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebrh->getNomEssencebrh() === $this) {
                $lignepagebrh->setNomEssencebrh(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lignepagelje>
     */
    public function getLignepageljes(): Collection
    {
        return $this->lignepageljes;
    }

    public function addLignepagelje(Lignepagelje $lignepagelje): static
    {
        if (!$this->lignepageljes->contains($lignepagelje)) {
            $this->lignepageljes->add($lignepagelje);
            $lignepagelje->setEssence($this);
        }

        return $this;
    }

    public function removeLignepagelje(Lignepagelje $lignepagelje): static
    {
        if ($this->lignepageljes->removeElement($lignepagelje)) {
            // set the owning side to null (unless already changed)
            if ($lignepagelje->getEssence() === $this) {
                $lignepagelje->setEssence(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lignepagebtgu>
     */
    public function getLignepagebtgus(): Collection
    {
        return $this->lignepagebtgus;
    }

    public function addLignepagebtgu(Lignepagebtgu $lignepagebtgu): static
    {
        if (!$this->lignepagebtgus->contains($lignepagebtgu)) {
            $this->lignepagebtgus->add($lignepagebtgu);
            $lignepagebtgu->setEssence($this);
        }

        return $this;
    }

    public function removeLignepagebtgu(Lignepagebtgu $lignepagebtgu): static
    {
        if ($this->lignepagebtgus->removeElement($lignepagebtgu)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebtgu->getEssence() === $this) {
                $lignepagebtgu->setEssence(null);
            }
        }

        return $this;
    }
}
