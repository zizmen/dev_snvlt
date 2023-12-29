<?php

namespace App\Entity\References;

use App\Entity\Administration\DocStatsGen;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Pages\Pagecp;
use App\Repository\References\PageDocGenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.page_doc_gen')]
#[ORM\Entity(repositoryClass: PageDocGenRepository::class)]
class PageDocGen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_page = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'pageDocGens')]
    private ?DocStatsGen $code_doc_gen = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $numpage = null;

    #[ORM\Column(nullable: true)]
    private ?int $doctype = null;

    #[ORM\Column(nullable: true)]
    private ?int $seqPage = null;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Pagecp::class)]
    private Collection $pagecps;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Pagebrh::class)]
    private Collection $pagebrhs;

    #[ORM\Column(nullable: true)]
    private ?bool $attribue = null;

    public function __construct()
    {
        $this->pagecps = new ArrayCollection();
        $this->pagebrhs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroPage(): ?string
    {
        return $this->numero_page;
    }

    public function setNumeroPage(string $numero_page): static
    {
        $this->numero_page = $numero_page;

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

    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    public function setUpdatedBy(?string $updated_by): static
    {
        $this->updated_by = $updated_by;

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

    public function getCodeDocGen(): ?DocStatsGen
    {
        return $this->code_doc_gen;
    }

    public function setCodeDocGen(?DocStatsGen $code_doc_gen): static
    {
        $this->code_doc_gen = $code_doc_gen;

        return $this;
    }

    public function getNumpage(): ?string
    {
        return $this->numpage;
    }

    public function setNumpage(?string $numpage): static
    {
        $this->numpage = $numpage;

        return $this;
    }

    public function getDoctype(): ?int
    {
        return $this->doctype;
    }

    public function setDoctype(?int $doctype): static
    {
        $this->doctype = $doctype;

        return $this;
    }

    public function getSeqPage(): ?int
    {
        return $this->seqPage;
    }

    public function setSeqPage(?int $seqPage): static
    {
        $this->seqPage = $seqPage;

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
            $pagecp->setCodeGeneration($this);
        }

        return $this;
    }

    public function removePagecp(Pagecp $pagecp): static
    {
        if ($this->pagecps->removeElement($pagecp)) {
            // set the owning side to null (unless already changed)
            if ($pagecp->getCodeGeneration() === $this) {
                $pagecp->setCodeGeneration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pagebrh>
     */
    public function getPagebrhs(): Collection
    {
        return $this->pagebrhs;
    }

    public function addPagebrh(Pagebrh $pagebrh): static
    {
        if (!$this->pagebrhs->contains($pagebrh)) {
            $this->pagebrhs->add($pagebrh);
            $pagebrh->setCodeGeneration($this);
        }

        return $this;
    }

    public function removePagebrh(Pagebrh $pagebrh): static
    {
        if ($this->pagebrhs->removeElement($pagebrh)) {
            // set the owning side to null (unless already changed)
            if ($pagebrh->getCodeGeneration() === $this) {
                $pagebrh->setCodeGeneration(null);
            }
        }

        return $this;
    }

    public function isAttribue(): ?bool
    {
        return $this->attribue;
    }

    public function setAttribue(?bool $attribue): static
    {
        $this->attribue = $attribue;

        return $this;
    }
}
