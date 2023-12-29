<?php

namespace App\Entity\References;

use App\Entity\DocStats\Entetes\Documentbtgu;
use App\Entity\DocStats\Entetes\Documentlje;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Pages\Pagebtgu;
use App\Entity\User;
use App\Repository\References\UsineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'metier.usine')]
#[ORM\Entity(repositoryClass: UsineRepository::class)]
#[UniqueEntity(fields: ['numero_usine'], message: 'The wood factory code already exists')]
#[UniqueEntity(fields: ['raison_sociale_usine'], message: 'There is already a name with this wood factory')]
#[UniqueEntity(fields: ['email_personne_ressource'], message: 'There is already an account with this email')]
class Usine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numero_usine = null;

    #[ORM\Column(length: 255)]
    private ?string $raison_sociale_usine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $personne_ressource = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $cc_usine = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $tel_usine = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $fax_usine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse_usine = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $localisation_usine = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(nullable: true)]
    private ?int $capacite_usine = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $sigle = null;

    #[ORM\ManyToOne(inversedBy: 'usines')]
    private ?Cantonnement $code_cantonnement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_personne_ressource = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $mobile_personne_ressource = null;

    #[ORM\Column(nullable: true)]
    private ?bool $export = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $code_exportateur = null;

    #[ORM\OneToMany(mappedBy: 'codeindustriel', targetEntity: User::class)]
    private Collection $utilisateurs;

    #[ORM\ManyToMany(targetEntity: TypeTransformation::class, inversedBy: 'usines')]
    private Collection $type_transformation;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'parc_usine_brh', targetEntity: Pagebrh::class)]
    private Collection $pagebrhs;

    #[ORM\OneToMany(mappedBy: 'code_usine', targetEntity: Documentlje::class)]
    private Collection $documentljes;

    #[ORM\OneToMany(mappedBy: 'code_usine', targetEntity: Documentbtgu::class)]
    private Collection $documentbtgus;

    #[ORM\OneToMany(mappedBy: 'usine_destinataire', targetEntity: Pagebtgu::class)]
    private Collection $pagebtgus;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
        $this->type_transformation = new ArrayCollection();
        $this->pagebrhs = new ArrayCollection();
        $this->documentljes = new ArrayCollection();
        $this->documentbtgus = new ArrayCollection();
        $this->pagebtgus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroUsine(): ?int
    {
        return $this->numero_usine;
    }

    public function setNumeroUsine(int $numero_usine): static
    {
        $this->numero_usine = $numero_usine;

        return $this;
    }

    public function getRaisonSocialeUsine(): ?string
    {
        return $this->raison_sociale_usine;
    }

    public function setRaisonSocialeUsine(string $raison_sociale_usine): static
    {
        $this->raison_sociale_usine = $raison_sociale_usine;

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

    public function getCcUsine(): ?string
    {
        return $this->cc_usine;
    }

    public function setCcUsine(?string $cc_usine): static
    {
        $this->cc_usine = $cc_usine;

        return $this;
    }

    public function getTelUsine(): ?string
    {
        return $this->tel_usine;
    }

    public function setTelUsine(?string $tel_usine): static
    {
        $this->tel_usine = $tel_usine;

        return $this;
    }

    public function getFaxUsine(): ?string
    {
        return $this->fax_usine;
    }

    public function setFaxUsine(?string $fax_usine): static
    {
        $this->fax_usine = $fax_usine;

        return $this;
    }

    public function getAdresseUsine(): ?string
    {
        return $this->adresse_usine;
    }

    public function setAdresseUsine(?string $adresse_usine): static
    {
        $this->adresse_usine = $adresse_usine;

        return $this;
    }

    public function getLocalisationUsine(): ?string
    {
        return $this->localisation_usine;
    }

    public function setLocalisationUsine(?string $localisation_usine): static
    {
        $this->localisation_usine = $localisation_usine;

        return $this;
    }


    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCapaciteUsine(): ?int
    {
        return $this->capacite_usine;
    }

    public function setCapaciteUsine(?int $capacite_usine): static
    {
        $this->capacite_usine = $capacite_usine;

        return $this;
    }

    public function getSigle(): ?string
    {
        return $this->sigle;
    }

    public function setSigle(?string $sigle): static
    {
        $this->sigle = $sigle;

        return $this;
    }

    public function getCodeCantonnement(): ?Cantonnement
    {
        return $this->code_cantonnement;
    }

    public function setCodeCantonnement(?Cantonnement $code_cantonnement): static
    {
        $this->code_cantonnement = $code_cantonnement;

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

    public function isExport(): ?bool
    {
        return $this->export;
    }

    public function setExport(?bool $export): static
    {
        $this->export = $export;

        return $this;
    }

    public function getCodeExportateur(): ?string
    {
        return $this->code_exportateur;
    }

    public function setCodeExportateur(?string $code_exportateur): static
    {
        $this->code_exportateur = $code_exportateur;

        return $this;
    }
    public function __toString(): string
    {
        return $this->raison_sociale_usine;
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
            $utilisateur->setCodeindustriel($this);
        }

        return $this;
    }

    public function removeUser(User $utilisateur): static
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getCodeindustriel() === $this) {
                $utilisateur->setCodeindustriel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TypeTransformation>
     */
    public function getTypeTransformation(): Collection
    {
        return $this->type_transformation;
    }

    public function addTypeTransformation(TypeTransformation $typeTransformation): static
    {
        if (!$this->type_transformation->contains($typeTransformation)) {
            $this->type_transformation->add($typeTransformation);
        }

        return $this;
    }

    public function removeTypeTransformation(TypeTransformation $typeTransformation): static
    {
        $this->type_transformation->removeElement($typeTransformation);

        return $this;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getUtilisateurs(): ArrayCollection|Collection
    {
        return $this->utilisateurs;
    }

    /**
     * @param ArrayCollection|Collection $utilisateurs
     */
    public function setUtilisateurs(ArrayCollection|Collection $utilisateurs): void
    {
        $this->utilisateurs = $utilisateurs;
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
            $pagebrh->setParcUsineBrh($this);
        }

        return $this;
    }

    public function removePagebrh(Pagebrh $pagebrh): static
    {
        if ($this->pagebrhs->removeElement($pagebrh)) {
            // set the owning side to null (unless already changed)
            if ($pagebrh->getParcUsineBrh() === $this) {
                $pagebrh->setParcUsineBrh(null);
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
            $documentlje->setCodeUsine($this);
        }

        return $this;
    }

    public function removeDocumentlje(Documentlje $documentlje): static
    {
        if ($this->documentljes->removeElement($documentlje)) {
            // set the owning side to null (unless already changed)
            if ($documentlje->getCodeUsine() === $this) {
                $documentlje->setCodeUsine(null);
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
            $documentbtgu->setCodeUsine($this);
        }

        return $this;
    }

    public function removeDocumentbtgu(Documentbtgu $documentbtgu): static
    {
        if ($this->documentbtgus->removeElement($documentbtgu)) {
            // set the owning side to null (unless already changed)
            if ($documentbtgu->getCodeUsine() === $this) {
                $documentbtgu->setCodeUsine(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pagebtgu>
     */
    public function getPagebtgus(): Collection
    {
        return $this->pagebtgus;
    }

    public function addPagebtgu(Pagebtgu $pagebtgu): static
    {
        if (!$this->pagebtgus->contains($pagebtgu)) {
            $this->pagebtgus->add($pagebtgu);
            $pagebtgu->setUsineDestinataire($this);
        }

        return $this;
    }

    public function removePagebtgu(Pagebtgu $pagebtgu): static
    {
        if ($this->pagebtgus->removeElement($pagebtgu)) {
            // set the owning side to null (unless already changed)
            if ($pagebtgu->getUsineDestinataire() === $this) {
                $pagebtgu->setUsineDestinataire(null);
            }
        }

        return $this;
    }


}
