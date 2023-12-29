<?php

namespace App\Entity\Administration;

use App\Entity\References\Essence;
use App\Entity\References\ZoneHemispherique;
use App\Entity\References\Foret;
use App\Repository\Administration\InventaireForestierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.inventaire_forestier')]
#[ORM\Entity(repositoryClass: InventaireForestierRepository::class)]
class InventaireForestier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'inventaireForestiers')]
    private ?Essence $code_essence = null;

    #[ORM\Column]
    private ?float $x = null;

    #[ORM\Column]
    private ?float $y = null;

    #[ORM\Column(nullable: true)]
    private ?float $dm = null;

    #[ORM\Column(nullable: true)]
    private ?float $lng = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume = null;

    #[ORM\ManyToOne(inversedBy: 'inventaireForestiers')]
    private ?foret $code_foret = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'inventaireForestiers')]
    private ?ZoneHemispherique $zoneh = null;

    #[ORM\ManyToOne(inversedBy: 'inventaireForestiers')]
    private ?FicheProspection $code_fiche_prospection = null;

    #[ORM\Column]
    private ?int $numero_arbre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeEssence(): ?Essence
    {
        return $this->code_essence;
    }

    public function setCodeEssence(?Essence $code_essence): static
    {
        $this->code_essence = $code_essence;

        return $this;
    }

    public function getX(): ?float
    {
        return $this->x;
    }

    public function setX(float $x): static
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?float
    {
        return $this->y;
    }

    public function setY(float $y): static
    {
        $this->y = $y;

        return $this;
    }

    public function getDm(): ?float
    {
        return $this->dm;
    }

    public function setDm(?float $dm): static
    {
        $this->dm = $dm;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(?float $lng): static
    {
        $this->lng = $lng;

        return $this;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function setVolume(?float $volume): static
    {
        $this->volume = $volume;

        return $this;
    }

    public function getCodeForet(): ?foret
    {
        return $this->code_foret;
    }

    public function setCodeForet(?foret $code_foret): static
    {
        $this->code_foret = $code_foret;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    public function setCreatedBy(string $created_by): static
    {
        $this->created_by = $created_by;

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

    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    public function setUpdatedBy(?string $updated_by): static
    {
        $this->updated_by = $updated_by;

        return $this;
    }

    public function getZoneh(): ?ZoneHemispherique
    {
        return $this->zoneh;
    }

    public function setZoneh(?ZoneHemispherique $zoneh): static
    {
        $this->zoneh = $zoneh;

        return $this;
    }

    public function getCodeFicheProspection(): ?FicheProspection
    {
        return $this->code_fiche_prospection;
    }

    public function setCodeFicheProspection(?FicheProspection $code_fiche_prospection): static
    {
        $this->code_fiche_prospection = $code_fiche_prospection;

        return $this;
    }

    public function getNumeroArbre(): ?int
    {
        return $this->numero_arbre;
    }

    public function setNumeroArbre(int $numero_arbre): static
    {
        $this->numero_arbre = $numero_arbre;

        return $this;
    }
}
