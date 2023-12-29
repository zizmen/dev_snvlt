<?php

namespace App\Entity\DocStats\Entetes;

use App\Entity\Administration\DocStatsGen;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Pages\Pagecp;
use App\Entity\References\TypeDocumentStatistique;
use App\Repository\DocStats\Entetes\DocumentcpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.documentcp')]
#[ORM\Entity(repositoryClass: DocumentcpRepository::class)]
class Documentcp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_doccp = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $delivre_doccp = null;

    #[ORM\ManyToOne(inversedBy: 'documentcps')]
    private ?TypeDocumentStatistique $type_document = null;

    #[ORM\ManyToOne(inversedBy: 'documentcps')]
    private ?Reprise $code_reprise = null;

    #[ORM\Column(nullable: true)]
    private ?int $exercice = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_doccp', targetEntity: Pagecp::class)]
    private Collection $pagecps;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\ManyToOne(inversedBy: 'documentcps')]
    private ?DocStatsGen $code_generation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $etat = null;

    public function __construct()
    {
        $this->pagecps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDoccp(): ?string
    {
        return $this->numero_doccp;
    }

    public function setNumeroDoccp(string $numero_doccp): static
    {
        $this->numero_doccp = $numero_doccp;

        return $this;
    }

    public function getDelivreDoccp(): ?\DateTimeInterface
    {
        return $this->delivre_doccp;
    }

    public function setDelivreDoccp(?\DateTimeInterface $delivre_doccp): static
    {
        $this->delivre_doccp = $delivre_doccp;

        return $this;
    }

    public function getTypeDocument(): ?TypeDocumentStatistique
    {
        return $this->type_document;
    }

    public function setTypeDocument(?TypeDocumentStatistique $type_document): static
    {
        $this->type_document = $type_document;

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

    public function getExercice(): ?int
    {
        return $this->exercice;
    }

    public function setExercice(?int $exercice): static
    {
        $this->exercice = $exercice;

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

    /**
     * @return Collection<int, Pagecp>
     */
    public function getPagecps(): Collection
    {
        return $this->pagecps;
    }

    public function addPagecp(Pagecp $pagecp): static
    {
        if (!$this->pagecps->contains($pagecp)) {
            $this->pagecps->add($pagecp);
            $pagecp->setCodeDoccp($this);
        }

        return $this;
    }

    public function removePagecp(Pagecp $pagecp): static
    {
        if ($this->pagecps->removeElement($pagecp)) {
            // set the owning side to null (unless already changed)
            if ($pagecp->getCodeDoccp() === $this) {
                $pagecp->setCodeDoccp(null);
            }
        }

        return $this;
    }

    public function getUniqueDoc(): ?string
    {
        return $this->unique_doc;
    }

    public function setUniqueDoc(?string $unique_doc): static
    {
        $this->unique_doc = $unique_doc;

        return $this;
    }

    public function getCodeGeneration(): ?DocStatsGen
    {
        return $this->code_generation;
    }

    public function setCodeGeneration(?DocStatsGen $code_generation): static
    {
        $this->code_generation = $code_generation;

        return $this;
    }

    public function isEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(?bool $etat): static
    {
        $this->etat = $etat;

        return $this;
    }
}
