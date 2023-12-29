<?php

namespace App\Entity\References;

use App\Repository\References\NatureProduitSecondaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.nature_produit_secondaire')]
#[ORM\Entity(repositoryClass: NatureProduitSecondaireRepository::class)]
class NatureProduitSecondaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nature_ps = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $unite = null;

    #[ORM\Column(nullable: true)]
    private ?int $montant_redevance = null;

    #[ORM\ManyToOne(inversedBy: 'natureProduitSecondaires')]
    private ?TypeProduitsSecondaires $type_dossier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNaturePs(): ?string
    {
        return $this->nature_ps;
    }

    public function setNaturePs(string $nature_ps): static
    {
        $this->nature_ps = $nature_ps;

        return $this;
    }

    public function getUnite(): ?string
    {
        return $this->unite;
    }

    public function setUnite(?string $unite): static
    {
        $this->unite = $unite;

        return $this;
    }

    public function getMontantRedevance(): ?int
    {
        return $this->montant_redevance;
    }

    public function setMontantRedevance(?int $montant_redevance): static
    {
        $this->montant_redevance = $montant_redevance;

        return $this;
    }

    public function getTypeDossier(): ?TypeProduitsSecondaires
    {
        return $this->type_dossier;
    }

    public function setTypeDossier(?TypeProduitsSecondaires $type_dossier): static
    {
        $this->type_dossier = $type_dossier;

        return $this;
    }
}
