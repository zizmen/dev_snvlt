<?php

namespace App\Entity\References;

use App\Entity\Autorisation\AgreementPs;
use App\Repository\References\TypeProduitsSecondairesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.type_produits_secondaires')]
#[ORM\Entity(repositoryClass: TypeProduitsSecondairesRepository::class)]
class TypeProduitsSecondaires
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'type_dossier', targetEntity: NatureProduitSecondaire::class)]
    private Collection $natureProduitSecondaires;

    #[ORM\OneToMany(mappedBy: 'code_type_produit_secondaire', targetEntity: AgreementPs::class)]
    private Collection $agreementPs;

    public function __construct()
    {
        $this->natureProduitSecondaires = new ArrayCollection();
        $this->agreementPs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, NatureProduitSecondaire>
     */
    public function getNatureProduitSecondaires(): Collection
    {
        return $this->natureProduitSecondaires;
    }

    public function addNatureProduitSecondaire(NatureProduitSecondaire $natureProduitSecondaire): static
    {
        if (!$this->natureProduitSecondaires->contains($natureProduitSecondaire)) {
            $this->natureProduitSecondaires->add($natureProduitSecondaire);
            $natureProduitSecondaire->setTypeDossier($this);
        }

        return $this;
    }

    public function removeNatureProduitSecondaire(NatureProduitSecondaire $natureProduitSecondaire): static
    {
        if ($this->natureProduitSecondaires->removeElement($natureProduitSecondaire)) {
            // set the owning side to null (unless already changed)
            if ($natureProduitSecondaire->getTypeDossier() === $this) {
                $natureProduitSecondaire->setTypeDossier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AgreementPs>
     */
    public function getAgreementPs(): Collection
    {
        return $this->agreementPs;
    }

    public function addAgreementP(AgreementPs $agreementP): static
    {
        if (!$this->agreementPs->contains($agreementP)) {
            $this->agreementPs->add($agreementP);
            $agreementP->setCodeTypeProduitSecondaire($this);
        }

        return $this;
    }

    public function removeAgreementP(AgreementPs $agreementP): static
    {
        if ($this->agreementPs->removeElement($agreementP)) {
            // set the owning side to null (unless already changed)
            if ($agreementP->getCodeTypeProduitSecondaire() === $this) {
                $agreementP->setCodeTypeProduitSecondaire(null);
            }
        }

        return $this;
    }
}
