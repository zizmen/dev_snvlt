<?php

namespace App\Entity\References;

use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\User;
use App\Repository\References\CantonnementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'metier.cantonnement')]
#[ORM\Entity(repositoryClass: CantonnementRepository::class)]
#[UniqueEntity(fields: ['email_personne_resource'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['nom_cantonnement'], message: 'There is already a name with this cantonnement')]
class Cantonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom_cantonnement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_responsable = null;

    #[ORM\ManyToOne(inversedBy: 'cantonnements')]
    private ?Dr $code_dr = null;

    #[ORM\OneToMany(mappedBy: 'code_cantonnement', targetEntity: Exploitant::class)]
    private Collection $exploitants;

    #[ORM\OneToMany(mappedBy: 'code_cantonnement', targetEntity: Usine::class)]
    private Collection $usines;

    #[ORM\OneToMany(mappedBy: 'code_direction', targetEntity: User::class)]
    private Collection $utilisateurs;

    #[ORM\ManyToOne(inversedBy: 'cantonnements')]
    private ?Ddef $code_ddef = null;

    #[ORM\OneToMany(mappedBy: 'code_cantonnement', targetEntity: PosteForestier::class)]
    private Collection $posteForestiers;

    #[ORM\OneToMany(mappedBy: 'code_cantonnement', targetEntity: Foret::class)]
    private Collection $forets;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'code_cantonnement', targetEntity: Exportateur::class)]
    private Collection $exportateurs;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $personne_ressource = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_personne_ressource = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mobile_personne_ressource = null;

    #[ORM\OneToMany(mappedBy: 'cantonnement_pagebrh', targetEntity: Pagebrh::class)]
    private Collection $pagebrhs;

    public function __construct()
    {
        $this->exploitants = new ArrayCollection();
        $this->usines = new ArrayCollection();
        $this->posteForestiers = new ArrayCollection();
        $this->forets = new ArrayCollection();
        $this->exportateurs = new ArrayCollection();
        $this->utilisateurs = new ArrayCollection();
        $this->pagebrhs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCantonnement(): ?string
    {
        return $this->nom_cantonnement;
    }

    public function setNomCantonnement(string $nom_cantonnement): static
    {
        $this->nom_cantonnement = $nom_cantonnement;

        return $this;
    }

    public function getEmailResponsable(): ?string
    {
        return $this->email_responsable;
    }

    public function setEmailResponsable(?string $email_responsable): static
    {
        $this->email_responsable = $email_responsable;

        return $this;
    }

    public function getCodeDr(): ?Dr
    {
        return $this->code_dr;
    }

    public function setCodeDr(?Dr $code_dr): static
    {
        $this->code_dr = $code_dr;

        return $this;
    }

    /**
     * @return Collection<int, Exploitant>
     */
    public function getExploitants(): Collection
    {
        return $this->exploitants;
    }

    public function addExploitant(Exploitant $exploitant): static
    {
        if (!$this->exploitants->contains($exploitant)) {
            $this->exploitants->add($exploitant);
            $exploitant->setCodeCantonnement($this);
        }

        return $this;
    }

    public function removeExploitant(Exploitant $exploitant): static
    {
        if ($this->exploitants->removeElement($exploitant)) {
            // set the owning side to null (unless already changed)
            if ($exploitant->getCodeCantonnement() === $this) {
                $exploitant->setCodeCantonnement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Usine>
     */
    public function getUsines(): Collection
    {
        return $this->usines;
    }

    public function addUsine(Usine $usine): static
    {
        if (!$this->usines->contains($usine)) {
            $this->usines->add($usine);
            $usine->setCodeCantonnement($this);
        }

        return $this;
    }

    public function removeUsine(Usine $usine): static
    {
        if ($this->usines->removeElement($usine)) {
            // set the owning side to null (unless already changed)
            if ($usine->getCodeCantonnement() === $this) {
                $usine->setCodeCantonnement(null);
            }
        }

        return $this;
    }

    public function getCodeDdef(): ?Ddef
    {
        return $this->code_ddef;
    }

    public function setCodeDdef(?Ddef $code_ddef): static
    {
        $this->code_ddef = $code_ddef;

        return $this;
    }
    public function __toString(): string
    {
        return $this->nom_cantonnement;
    }

    /**
     * @return Collection<int, PosteForestier>
     */
    public function getPosteForestiers(): Collection
    {
        return $this->posteForestiers;
    }

    public function addPosteForestier(PosteForestier $posteForestier): static
    {
        if (!$this->posteForestiers->contains($posteForestier)) {
            $this->posteForestiers->add($posteForestier);
            $posteForestier->setCodeCantonnement($this);
        }

        return $this;
    }

    public function removePosteForestier(PosteForestier $posteForestier): static
    {
        if ($this->posteForestiers->removeElement($posteForestier)) {
            // set the owning side to null (unless already changed)
            if ($posteForestier->getCodeCantonnement() === $this) {
                $posteForestier->setCodeCantonnement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Foret>
     */
    public function getForets(): Collection
    {
        return $this->forets;
    }

    public function addForet(Foret $foret): static
    {
        if (!$this->forets->contains($foret)) {
            $this->forets->add($foret);
            $foret->setCodeCantonnement($this);
        }

        return $this;
    }

    public function removeForet(Foret $foret): static
    {
        if ($this->forets->removeElement($foret)) {
            // set the owning side to null (unless already changed)
            if ($foret->getCodeCantonnement() === $this) {
                $foret->setCodeCantonnement(null);
            }
        }

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @param \DateTimeImmutable|null $created_at
     */
    public function setCreatedAt(?\DateTimeImmutable $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return string|null
     */
    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    /**
     * @param string|null $created_by
     */
    public function setCreatedBy(?string $created_by): void
    {
        $this->created_by = $created_by;
    }

    /**
     * @return string|null
     */
    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    /**
     * @param string|null $updated_by
     */
    public function setUpdatedBy(?string $updated_by): void
    {
        $this->updated_by = $updated_by;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * @param \DateTimeInterface|null $updated_at
     */
    public function setUpdatedAt(?\DateTimeInterface $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return Collection<int, Exportateur>
     */
    public function getExportateurs(): Collection
    {
        return $this->exportateurs;
    }

    public function addExportateur(Exportateur $exportateur): static
    {
        if (!$this->exportateurs->contains($exportateur)) {
            $this->exportateurs->add($exportateur);
            $exportateur->setCodeCantonnement($this);
        }

        return $this;
    }

    public function removeExportateur(Exportateur $exportateur): static
    {
        if ($this->exportateurs->removeElement($exportateur)) {
            // set the owning side to null (unless already changed)
            if ($exportateur->getCodeCantonnement() === $this) {
                $exportateur->setCodeCantonnement(null);
            }
        }

        return $this;
    }

    public function getPersonneRessource(): ?string
    {
        return $this->personne_ressource;
    }

    public function setPersonneRessource(?string $personne_ressource): static
    {
        $this->personne_ressource = $personne_ressource;

        return $this;
    }

    public function getEmailPersonneRessource(): ?string
    {
        return $this->email_personne_ressource;
    }

    public function setEmailPersonneRessource(?string $email_personne_ressource): static
    {
        $this->email_personne_ressource = $email_personne_ressource;

        return $this;
    }

    public function getMobilePersonneRessource(): ?string
    {
        return $this->mobile_personne_ressource;
    }

    public function setMobilePersonneRessource(?string $mobile_personne_ressource): static
    {
        $this->mobile_personne_ressource = $mobile_personne_ressource;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUser(User $utilisateur): static
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->add($utilisateur);
            $utilisateur->setCodeCantonnement($this);
        }

        return $this;
    }

    public function removeUser(User $utilisateur): static
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getCodeCantonnement() === $this) {
                $utilisateur->setCodeCantonnement(null);
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
            $pagebrh->setCantonnementPagebrh($this);
        }

        return $this;
    }

    public function removePagebrh(Pagebrh $pagebrh): static
    {
        if ($this->pagebrhs->removeElement($pagebrh)) {
            // set the owning side to null (unless already changed)
            if ($pagebrh->getCantonnementPagebrh() === $this) {
                $pagebrh->setCantonnementPagebrh(null);
            }
        }

        return $this;
    }
}
