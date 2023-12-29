<?php

namespace App\Entity\Autorisation;

use App\Entity\References\AttributairePs;
use App\Entity\References\TypeProduitsSecondaires;
use App\Repository\Autorisation\AgreementPsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.agreement_ps')]
#[ORM\Entity(repositoryClass: AgreementPsRepository::class)]
class AgreementPs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_dossier = null;

    #[ORM\Column(nullable: true)]
    private ?int $montant_agrement = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_ouverture = null;

    #[ORM\ManyToOne(inversedBy: 'agreementPs')]
    private ?TypeProduitsSecondaires $code_type_produit_secondaire = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'agreementPs')]
    private ?AttributairePs $code_attributaire_ps = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDossier(): ?string
    {
        return $this->numero_dossier;
    }

    public function setNumeroDossier(string $numero_dossier): static
    {
        $this->numero_dossier = $numero_dossier;

        return $this;
    }

    public function getMontantAgrement(): ?int
    {
        return $this->montant_agrement;
    }

    public function setMontantAgrement(?int $montant_agrement): static
    {
        $this->montant_agrement = $montant_agrement;

        return $this;
    }

    public function getDateOuverture(): ?\DateTimeInterface
    {
        return $this->date_ouverture;
    }

    public function setDateOuverture(?\DateTimeInterface $date_ouverture): static
    {
        $this->date_ouverture = $date_ouverture;

        return $this;
    }

    public function getCodeTypeProduitSecondaire(): ?TypeProduitsSecondaires
    {
        return $this->code_type_produit_secondaire;
    }

    public function setCodeTypeProduitSecondaire(?TypeProduitsSecondaires $code_type_produit_secondaire): static
    {
        $this->code_type_produit_secondaire = $code_type_produit_secondaire;

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

    public function setCreatedBy(?string $created_by): static
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

    public function getCodeAttributairePs(): ?AttributairePs
    {
        return $this->code_attributaire_ps;
    }

    public function setCodeAttributairePs(?AttributairePs $code_attributaire_ps): static
    {
        $this->code_attributaire_ps = $code_attributaire_ps;

        return $this;
    }
}
