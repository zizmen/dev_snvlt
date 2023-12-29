<?php

namespace App\Entity\Administration;

use App\Entity\Autorisation\Reprise;
use App\Entity\References\CircuitCommunication;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\References\TypeOperateur;
use App\Entity\User;
use App\Repository\DemandeOperateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.demande_operateur')]
#[ORM\Entity(repositoryClass: DemandeOperateurRepository::class)]
class DemandeOperateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'demandeOperateurs')]
    private ?TypeDocumentStatistique $doc_stat = null;

    #[ORM\Column(nullable: true)]
    private ?int $qte = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $statut = null;

    #[ORM\Column(nullable: true)]
    private ?bool $transmission = null;

    #[ORM\Column(nullable: true)]
    private ?bool $verification = null;

    #[ORM\Column(nullable: true)]
    private ?bool $delivrance = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_verification = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_delivrance = null;

    #[ORM\ManyToOne(inversedBy: 'demandeOperateurs')]
    private ?User $demandeur = null;

    #[ORM\Column(nullable: true)]
    private ?int $qte_validee = null;

    #[ORM\Column(nullable: true)]
    private ?int $qte_delivree = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $motif_verification = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $motif_delivrance = null;

    #[ORM\ManyToOne(inversedBy: 'demandeOperateurs')]
    private ?TypeOperateur $code_operateur = null;

    #[ORM\Column(nullable: true)]
    private ?int $code_structure = null;

    #[ORM\ManyToOne(inversedBy: 'demandeOperateurs')]
    private ?Reprise $code_reprise = null;

    #[ORM\OneToMany(mappedBy: 'code_demande_operateur', targetEntity: CircuitCommunication::class)]
    private Collection $circuitCommunications;

    #[ORM\Column(nullable: true)]
    private ?bool $docs_generes = null;

    public function __construct()
    {
        $this->circuitCommunications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocStat(): ?TypeDocumentStatistique
    {
        return $this->doc_stat;
    }

    public function setDocStat(?TypeDocumentStatistique $doc_stat): static
    {
        $this->doc_stat = $doc_stat;

        return $this;
    }

    public function getQte(): ?int
    {
        return $this->qte;
    }

    public function setQte(?int $qte): static
    {
        $this->qte = $qte;

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

    public function setUpdatedBy(string $updated_by): static
    {
        $this->updated_by = $updated_by;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function isTransmission(): ?bool
    {
        return $this->transmission;
    }

    public function setTransmission(?bool $transmission): static
    {
        $this->transmission = $transmission;

        return $this;
    }

    public function isVerification(): ?bool
    {
        return $this->verification;
    }

    public function setVerification(?bool $verification): static
    {
        $this->verification = $verification;

        return $this;
    }

    public function isDelivrance(): ?bool
    {
        return $this->delivrance;
    }

    public function setDelivrance(?bool $delivrance): static
    {
        $this->delivrance = $delivrance;

        return $this;
    }

    public function getDateVerification(): ?\DateTimeInterface
    {
        return $this->date_verification;
    }

    public function setDateVerification(?\DateTimeInterface $date_verification): static
    {
        $this->date_verification = $date_verification;

        return $this;
    }

    public function getDateDelivrance(): ?\DateTimeInterface
    {
        return $this->date_delivrance;
    }

    public function setDateDelivrance(?\DateTimeInterface $date_delivrance): static
    {
        $this->date_delivrance = $date_delivrance;

        return $this;
    }

    public function getDemandeur(): ?User
    {
        return $this->demandeur;
    }

    public function setDemandeur(?User $demandeur): static
    {
        $this->demandeur = $demandeur;

        return $this;
    }

    public function getQteValidee(): ?int
    {
        return $this->qte_validee;
    }

    public function setQteValidee(?int $qte_validee): static
    {
        $this->qte_validee = $qte_validee;

        return $this;
    }

    public function getQteDelivree(): ?int
    {
        return $this->qte_delivree;
    }

    public function setQteDelivree(?int $qte_delivree): static
    {
        $this->qte_delivree = $qte_delivree;

        return $this;
    }

    public function getMotifVerification(): ?string
    {
        return $this->motif_verification;
    }

    public function setMotifVerification(?string $motif_verification): static
    {
        $this->motif_verification = $motif_verification;

        return $this;
    }

    public function getMotifDelivrance(): ?string
    {
        return $this->motif_delivrance;
    }

    public function setMotifDelivrance(?string $motif_delivrance): static
    {
        $this->motif_delivrance = $motif_delivrance;

        return $this;
    }

    public function getCodeOperateur(): ?TypeOperateur
    {
        return $this->code_operateur;
    }

    public function setCodeOperateur(?TypeOperateur $code_operateur): static
    {
        $this->code_operateur = $code_operateur;

        return $this;
    }

    public function getCodeStructure(): ?int
    {
        return $this->code_structure;
    }

    public function setCodeStructure(?int $code_structure): static
    {
        $this->code_structure = $code_structure;

        return $this;
    }

    public function getCodeReprise(): ?Reprise
    {
        return $this->code_reprise;
    }

    public function setCodeReprise(?Reprise $code_reprise): static
    {
        $this->code_reprise = $code_reprise;

        return $this;
    }

    /**
     * @return Collection<int, CircuitCommunication>
     */
    public function getCircuitCommunications(): Collection
    {
        return $this->circuitCommunications;
    }

    public function addCircuitCommunication(CircuitCommunication $circuitCommunication): static
    {
        if (!$this->circuitCommunications->contains($circuitCommunication)) {
            $this->circuitCommunications->add($circuitCommunication);
            $circuitCommunication->setCodeDemandeOperateur($this);
        }

        return $this;
    }

    public function removeCircuitCommunication(CircuitCommunication $circuitCommunication): static
    {
        if ($this->circuitCommunications->removeElement($circuitCommunication)) {
            // set the owning side to null (unless already changed)
            if ($circuitCommunication->getCodeDemandeOperateur() === $this) {
                $circuitCommunication->setCodeDemandeOperateur(null);
            }
        }

        return $this;
    }

    public function isDocsGeneres(): ?bool
    {
        return $this->docs_generes;
    }

    public function setDocsGeneres(?bool $docs_generes): static
    {
        $this->docs_generes = $docs_generes;

        return $this;
    }
}
