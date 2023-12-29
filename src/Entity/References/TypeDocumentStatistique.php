<?php

namespace App\Entity\References;

use App\Entity\Administration\DemandeOperateur;
use App\Entity\Administration\DocStatsGen;
use App\Entity\Administration\StockDoc;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Entetes\Documentbtgu;
use App\Entity\DocStats\Entetes\Documentcp;
use App\Entity\DocStats\Entetes\Documentlje;
use App\Entity\DocStats\Saisie\Lignepagelje;
use App\Repository\References\TypeDocumentStatistiqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'metier.type_document_statistique')]
#[ORM\Entity(repositoryClass: TypeDocumentStatistiqueRepository::class)]
#[UniqueEntity(fields: ['abv'], message: 'There is already a document with this name')]
class TypeDocumentStatistique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $abv = null;

    #[ORM\Column(length: 100)]
    private ?string $denomination = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_type_doc_stat', targetEntity: StockDoc::class)]
    private Collection $stockDocs;

    #[ORM\OneToMany(mappedBy: 'docname', targetEntity: DocStatsGen::class)]
    private Collection $docStatsGens;

    #[ORM\Column(nullable: true)]
    private ?int $nb_pages = null;

    #[ORM\Column(nullable: true)]
    private ?int $tarif = null;

    #[ORM\OneToMany(mappedBy: 'doc_stat', targetEntity: DemandeOperateur::class)]
    private Collection $demandeOperateurs;

    #[ORM\ManyToOne(inversedBy: 'typeDocumentStatistiques')]
    private ?TypeOperateur $code_type_operateur = null;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentcp::class)]
    private Collection $documentcps;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentbrh::class)]
    private Collection $documentbrhs;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentlje::class)]
    private Collection $documentljes;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentbtgu::class)]
    private Collection $documentbtgus;

    #[ORM\OneToMany(mappedBy: 'code_type_doc', targetEntity: Lignepagelje::class)]
    private Collection $lignepageljes;

    public function __construct()
    {
        $this->stockDocs = new ArrayCollection();
        $this->docStatsGens = new ArrayCollection();
        $this->demandeOperateurs = new ArrayCollection();
        $this->documentcps = new ArrayCollection();
        $this->documentbrhs = new ArrayCollection();
        $this->documentljes = new ArrayCollection();
        $this->documentbtgus = new ArrayCollection();
        $this->lignepageljes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbv(): ?string
    {
        return $this->abv;
    }

    public function setAbv(string $abv): static
    {
        $this->abv = $abv;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
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

    /**
     * @return Collection<int, StockDoc>
     */
    public function getStockDocs(): Collection
    {
        return $this->stockDocs;
    }

    public function addStockDoc(StockDoc $stockDoc): static
    {
        if (!$this->stockDocs->contains($stockDoc)) {
            $this->stockDocs->add($stockDoc);
            $stockDoc->setCodeTypeDocStat($this);
        }

        return $this;
    }

    public function removeStockDoc(StockDoc $stockDoc): static
    {
        if ($this->stockDocs->removeElement($stockDoc)) {
            // set the owning side to null (unless already changed)
            if ($stockDoc->getCodeTypeDocStat() === $this) {
                $stockDoc->setCodeTypeDocStat(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->abv;
    }

    /**
     * @return Collection<int, DocStatsGen>
     */
    public function getDocStatsGens(): Collection
    {
        return $this->docStatsGens;
    }

    public function addDocStatsGen(DocStatsGen $docStatsGen): static
    {
        if (!$this->docStatsGens->contains($docStatsGen)) {
            $this->docStatsGens->add($docStatsGen);
            $docStatsGen->setDocname($this);
        }

        return $this;
    }

    public function removeDocStatsGen(DocStatsGen $docStatsGen): static
    {
        if ($this->docStatsGens->removeElement($docStatsGen)) {
            // set the owning side to null (unless already changed)
            if ($docStatsGen->getDocname() === $this) {
                $docStatsGen->setDocname(null);
            }
        }

        return $this;
    }

    public function getNbPages(): ?int
    {
        return $this->nb_pages;
    }

    public function setNbPages(?int $nb_pages): static
    {
        $this->nb_pages = $nb_pages;

        return $this;
    }

    public function getTarif(): ?int
    {
        return $this->tarif;
    }

    public function setTarif(?int $tarif): static
    {
        $this->tarif = $tarif;

        return $this;
    }

    /**
     * @return Collection<int, DemandeOperateur>
     */
    public function getDemandeOperateurs(): Collection
    {
        return $this->demandeOperateurs;
    }

    public function addDemandeOperateur(DemandeOperateur $demandeOperateur): static
    {
        if (!$this->demandeOperateurs->contains($demandeOperateur)) {
            $this->demandeOperateurs->add($demandeOperateur);
            $demandeOperateur->setDocStat($this);
        }

        return $this;
    }

    public function removeDemandeOperateur(DemandeOperateur $demandeOperateur): static
    {
        if ($this->demandeOperateurs->removeElement($demandeOperateur)) {
            // set the owning side to null (unless already changed)
            if ($demandeOperateur->getDocStat() === $this) {
                $demandeOperateur->setDocStat(null);
            }
        }

        return $this;
    }

    public function getCodeTypeOperateur(): ?TypeOperateur
    {
        return $this->code_type_operateur;
    }

    public function setCodeTypeOperateur(?TypeOperateur $code_type_operateur): static
    {
        $this->code_type_operateur = $code_type_operateur;

        return $this;
    }

    /**
     * @return Collection<int, Documentcp>
     */
    public function getDocumentcps(): Collection
    {
        return $this->documentcps;
    }

    public function addDocumentcp(Documentcp $documentcp): static
    {
        if (!$this->documentcps->contains($documentcp)) {
            $this->documentcps->add($documentcp);
            $documentcp->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentcp(Documentcp $documentcp): static
    {
        if ($this->documentcps->removeElement($documentcp)) {
            // set the owning side to null (unless already changed)
            if ($documentcp->getTypeDocument() === $this) {
                $documentcp->setTypeDocument(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentbrh>
     */
    public function getDocumentbrhs(): Collection
    {
        return $this->documentbrhs;
    }

    public function addDocumentbrh(Documentbrh $documentbrh): static
    {
        if (!$this->documentbrhs->contains($documentbrh)) {
            $this->documentbrhs->add($documentbrh);
            $documentbrh->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentbrh(Documentbrh $documentbrh): static
    {
        if ($this->documentbrhs->removeElement($documentbrh)) {
            // set the owning side to null (unless already changed)
            if ($documentbrh->getTypeDocument() === $this) {
                $documentbrh->setTypeDocument(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentlje>
     */
    public function getDocumentljes(): Collection
    {
        return $this->documentljes;
    }

    public function addDocumentlje(Documentlje $documentlje): static
    {
        if (!$this->documentljes->contains($documentlje)) {
            $this->documentljes->add($documentlje);
            $documentlje->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentlje(Documentlje $documentlje): static
    {
        if ($this->documentljes->removeElement($documentlje)) {
            // set the owning side to null (unless already changed)
            if ($documentlje->getTypeDocument() === $this) {
                $documentlje->setTypeDocument(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentbtgu>
     */
    public function getDocumentbtgus(): Collection
    {
        return $this->documentbtgus;
    }

    public function addDocumentbtgu(Documentbtgu $documentbtgu): static
    {
        if (!$this->documentbtgus->contains($documentbtgu)) {
            $this->documentbtgus->add($documentbtgu);
            $documentbtgu->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentbtgu(Documentbtgu $documentbtgu): static
    {
        if ($this->documentbtgus->removeElement($documentbtgu)) {
            // set the owning side to null (unless already changed)
            if ($documentbtgu->getTypeDocument() === $this) {
                $documentbtgu->setTypeDocument(null);
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
            $lignepagelje->setCodeTypeDoc($this);
        }

        return $this;
    }

    public function removeLignepagelje(Lignepagelje $lignepagelje): static
    {
        if ($this->lignepageljes->removeElement($lignepagelje)) {
            // set the owning side to null (unless already changed)
            if ($lignepagelje->getCodeTypeDoc() === $this) {
                $lignepagelje->setCodeTypeDoc(null);
            }
        }

        return $this;
    }
}
