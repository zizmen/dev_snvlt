<?php

namespace App\Entity\DocStats\Entetes;

use App\Entity\Admin\Exercice;
use App\Entity\Administration\DocStatsGen;
use App\Entity\DocStats\Pages\Pagelje;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\References\Usine;
use App\Repository\DocStats\Entetes\DocumentljeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.documentlje')]
#[ORM\Entity(repositoryClass: DocumentljeRepository::class)]
class Documentlje
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_doclje = null;

    #[ORM\ManyToOne(inversedBy: 'documentljes')]
    private ?Usine $code_usine = null;

    #[ORM\ManyToOne(inversedBy: 'documentljes')]
    private ?Exercice $exercice = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $delivre_doclje = null;

    #[ORM\ManyToOne(inversedBy: 'documentljes')]
    private ?DocStatsGen $code_generation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $etat = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\ManyToOne(inversedBy: 'documentljes')]
    private ?TypeDocumentStatistique $type_document = null;

    #[ORM\OneToMany(mappedBy: 'code_doclje', targetEntity: Pagelje::class)]
    private Collection $pageljes;

    public function __construct()
    {
        $this->pageljes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDoclje(): ?string
    {
        return $this->numero_doclje;
    }

    public function setNumeroDoclje(string $numero_doclje): static
    {
        $this->numero_doclje = $numero_doclje;

        return $this;
    }

    public function getCodeUsine(): ?Usine
    {
        return $this->code_usine;
    }

    public function setCodeUsine(?Usine $code_usine): static
    {
        $this->code_usine = $code_usine;

        return $this;
    }

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): static
    {
        $this->exercice = $exercice;

        return $this;
    }

    public function getDelivreDoclje(): ?\DateTimeInterface
    {
        return $this->delivre_doclje;
    }

    public function setDelivreDoclje(?\DateTimeInterface $delivre_doclje): static
    {
        $this->delivre_doclje = $delivre_doclje;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): static
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

    public function getUniqueDoc(): ?string
    {
        return $this->unique_doc;
    }

    public function setUniqueDoc(?string $unique_doc): static
    {
        $this->unique_doc = $unique_doc;

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

    public function __toString(): string
    {
        return $this->numero_doclje;
    }

    /**
     * @return Collection<int, Pagelje>
     */
    public function getPageljes(): Collection
    {
        return $this->pageljes;
    }

    public function addPagelje(Pagelje $pagelje): static
    {
        if (!$this->pageljes->contains($pagelje)) {
            $this->pageljes->add($pagelje);
            $pagelje->setCodeDoclje($this);
        }

        return $this;
    }

    public function removePagelje(Pagelje $pagelje): static
    {
        if ($this->pageljes->removeElement($pagelje)) {
            // set the owning side to null (unless already changed)
            if ($pagelje->getCodeDoclje() === $this) {
                $pagelje->setCodeDoclje(null);
            }
        }

        return $this;
    }
}
