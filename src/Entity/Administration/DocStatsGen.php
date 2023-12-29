<?php

namespace App\Entity\Administration;

use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Entetes\Documentbtgu;
use App\Entity\DocStats\Entetes\Documentcp;
use App\Entity\DocStats\Entetes\Documentlje;
use App\Entity\References\PageDocGen;
use App\Entity\References\TypeDocumentStatistique;
use App\Repository\Administration\DocStatsGenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.doc_stats_gen')]
#[ORM\Entity(repositoryClass: DocStatsGenRepository::class)]
class DocStatsGen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1)]
    private ?string $lettre = null;

    #[ORM\Column]
    private ?int $annee = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_doc = null;

    #[ORM\ManyToOne(inversedBy: 'docStatsGens')]
    private ?TypeDocumentStatistique $docname = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_doc_gen', targetEntity: PageDocGen::class)]
    private Collection $pageDocGens;

    #[ORM\ManyToOne(inversedBy: 'docStatsGens')]
    private ?StockDoc $code_type_doc = null;

    #[ORM\Column(nullable: true)]
    private ?bool $attribue = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $uniqueDoc = null;

    #[ORM\Column(nullable: true)]
    private ?int $numdoc = null;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentcp::class)]
    private Collection $documentcps;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentbrh::class)]
    private Collection $documentbrhs;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentlje::class)]
    private Collection $documentljes;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentbtgu::class)]
    private Collection $documentbtgus;

    public function __construct()
    {
        $this->pageDocGens = new ArrayCollection();
        $this->documentcps = new ArrayCollection();
        $this->documentbrhs = new ArrayCollection();
        $this->documentljes = new ArrayCollection();
        $this->documentbtgus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLettre(): ?string
    {
        return $this->lettre;
    }

    public function setLettre(string $lettre): static
    {
        $this->lettre = $lettre;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getNumeroDoc(): ?string
    {
        return $this->numero_doc;
    }

    public function setNumeroDoc(string $numero_doc): static
    {
        $this->numero_doc = $numero_doc;

        return $this;
    }

    public function getDocname(): ?TypeDocumentStatistique
    {
        return $this->docname;
    }

    public function setDocname(?TypeDocumentStatistique $docname): static
    {
        $this->docname = $docname;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): static
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
     * @return Collection<int, PageDocGen>
     */
    public function getPageDocGens(): Collection
    {
        return $this->pageDocGens;
    }

    public function addPageDocGen(PageDocGen $pageDocGen): static
    {
        if (!$this->pageDocGens->contains($pageDocGen)) {
            $this->pageDocGens->add($pageDocGen);
            $pageDocGen->setCodeDocGen($this);
        }

        return $this;
    }

    public function removePageDocGen(PageDocGen $pageDocGen): static
    {
        if ($this->pageDocGens->removeElement($pageDocGen)) {
            // set the owning side to null (unless already changed)
            if ($pageDocGen->getCodeDocGen() === $this) {
                $pageDocGen->setCodeDocGen(null);
            }
        }

        return $this;
    }

    public function getCodeTypeDoc(): ?StockDoc
    {
        return $this->code_type_doc;
    }

    public function setCodeTypeDoc(?StockDoc $code_type_doc): static
    {
        $this->code_type_doc = $code_type_doc;

        return $this;
    }

    public function isAttribue(): ?bool
    {
        return $this->attribue;
    }

    public function setAttribue(bool $attribue): static
    {
        $this->attribue = $attribue;

        return $this;
    }

    public function getUniqueDoc(): ?string
    {
        return $this->uniqueDoc;
    }

    public function setUniqueDoc(?string $uniqueDoc): static
    {
        $this->uniqueDoc = $uniqueDoc;

        return $this;
    }

    public function getNumdoc(): ?int
    {
        return $this->numdoc;
    }

    public function setNumdoc(?int $numdoc): static
    {
        $this->numdoc = $numdoc;

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
            $documentcp->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentcp(Documentcp $documentcp): static
    {
        if ($this->documentcps->removeElement($documentcp)) {
            // set the owning side to null (unless already changed)
            if ($documentcp->getCodeGeneration() === $this) {
                $documentcp->setCodeGeneration(null);
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
            $documentbrh->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentbrh(Documentbrh $documentbrh): static
    {
        if ($this->documentbrhs->removeElement($documentbrh)) {
            // set the owning side to null (unless already changed)
            if ($documentbrh->getCodeGeneration() === $this) {
                $documentbrh->setCodeGeneration(null);
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
            $documentlje->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentlje(Documentlje $documentlje): static
    {
        if ($this->documentljes->removeElement($documentlje)) {
            // set the owning side to null (unless already changed)
            if ($documentlje->getCodeGeneration() === $this) {
                $documentlje->setCodeGeneration(null);
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
            $documentbtgu->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentbtgu(Documentbtgu $documentbtgu): static
    {
        if ($this->documentbtgus->removeElement($documentbtgu)) {
            // set the owning side to null (unless already changed)
            if ($documentbtgu->getCodeGeneration() === $this) {
                $documentbtgu->setCodeGeneration(null);
            }
        }

        return $this;
    }
}
