<?php

namespace App\Entity\References;

use App\Entity\References\Ddef;
use App\Entity\User;
use App\Repository\References\DrRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'metier.dr')]
#[ORM\Entity(repositoryClass: DrRepository::class)]
#[UniqueEntity(fields: ['email_responsable'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['denomination'], message: 'There is already a name with this regional directorate')]
class Dr
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $denomination = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_responsable = null;

    #[ORM\OneToMany(mappedBy: 'code_dr', targetEntity: Cantonnement::class)]
    private Collection $cantonnements;

    #[ORM\OneToMany(mappedBy: 'code_dr', targetEntity: User::class)]
    private Collection $utilisateurs;

    #[ORM\OneToMany(mappedBy: 'code_dr', targetEntity: Ddef::class)]
    private Collection $ddefs;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $personne_ressource = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_personne_ressource = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mobile_personne_ressource = null;


    public function __construct()
    {
        $this->cantonnements = new ArrayCollection();
        $this->utilisateurs = new ArrayCollection();
        $this->ddefs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmailResponsable(): ?string
    {
        return $this->email_responsable;
    }

    public function setEmailResponsable(?string $email_responsable): static
    {
        $this->email_responsable = $email_responsable;

        return $this;
    }

    /**
     * @return Collection<int, Cantonnement>
     */
    public function getCantonnements(): Collection
    {
        return $this->cantonnements;
    }

    public function addCantonnement(Cantonnement $cantonnement): static
    {
        if (!$this->cantonnements->contains($cantonnement)) {
            $this->cantonnements->add($cantonnement);
            $cantonnement->setCodeDr($this);
        }

        return $this;
    }

    public function removeCantonnement(Cantonnement $cantonnement): static
    {
        if ($this->cantonnements->removeElement($cantonnement)) {
            // set the owning side to null (unless already changed)
            if ($cantonnement->getCodeDr() === $this) {
                $cantonnement->setCodeDr(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->denomination;
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
            $utilisateur->setCodeDr($this);
        }

        return $this;
    }

    public function removeUser(User $utilisateur): static
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getCodeDr() === $this) {
                $utilisateur->setCodeDr(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ddef>
     */
    public function getDdefs(): Collection
    {
        return $this->ddefs;
    }

    public function addDdef(Ddef $ddef): static
    {
        if (!$this->ddefs->contains($ddef)) {
            $this->ddefs->add($ddef);
            $ddef->setCodeDr($this);
        }

        return $this;
    }

    public function removeDdef(Ddef $ddef): static
    {
        if ($this->ddefs->removeElement($ddef)) {
            // set the owning side to null (unless already changed)
            if ($ddef->getCodeDr() === $this) {
                $ddef->setCodeDr(null);
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
}
