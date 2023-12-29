<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\DocStats\Entetes\Documentbtgu;
use App\Entity\DocStats\Saisie\Lignepagebtgu;
use App\Entity\References\Usine;
use App\Repository\DocStats\Pages\PagebtguRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.pagebtgu')]
#[ORM\Entity(repositoryClass: PagebtguRepository::class)]
class Pagebtgu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_pagebtgu = null;

    #[ORM\Column(nullable: true)]
    private ?int $index_pagebtgu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $transporteur = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $immatriculation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $chauffeur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datechargement = null;

    #[ORM\ManyToOne(inversedBy: 'pagebtgus')]
    private ?Usine $usine_destinataire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $destination = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_depart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_arrivee = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\Column(nullable: true)]
    private ?int $mois = null;

    #[ORM\Column(nullable: true)]
    private ?bool $confirmation_usine = null;

    #[ORM\ManyToOne(inversedBy: 'pagebtgus')]
    private ?Documentbtgu $code_docbtgu = null;

    #[ORM\OneToMany(mappedBy: 'code_pagebtgu', targetEntity: Lignepagebtgu::class)]
    private Collection $lignepagebtgus;

    public function __construct()
    {
        $this->lignepagebtgus = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroPagebtgu(): ?string
    {
        return $this->numero_pagebtgu;
    }

    public function setNumeroPagebtgu(string $numero_pagebtgu): static
    {
        $this->numero_pagebtgu = $numero_pagebtgu;

        return $this;
    }

    public function getIndexPagebtgu(): ?int
    {
        return $this->index_pagebtgu;
    }

    public function setIndexPagebtgu(?int $index_pagebtgu): static
    {
        $this->index_pagebtgu = $index_pagebtgu;

        return $this;
    }

    public function getTransporteur(): ?string
    {
        return $this->transporteur;
    }

    public function setTransporteur(?string $transporteur): static
    {
        $this->transporteur = $transporteur;

        return $this;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(?string $immatriculation): static
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getChauffeur(): ?string
    {
        return $this->chauffeur;
    }

    public function setChauffeur(?string $chauffeur): static
    {
        $this->chauffeur = $chauffeur;

        return $this;
    }

    public function getDatechargement(): ?\DateTimeInterface
    {
        return $this->datechargement;
    }

    public function setDatechargement(?\DateTimeInterface $datechargement): static
    {
        $this->datechargement = $datechargement;

        return $this;
    }

    public function getUsineDestinataire(): ?Usine
    {
        return $this->usine_destinataire;
    }

    public function setUsineDestinataire(?Usine $usine_destinataire): static
    {
        $this->usine_destinataire = $usine_destinataire;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(?string $destination): static
    {
        $this->destination = $destination;

        return $this;
    }

    public function getDateDepart(): ?\DateTimeInterface
    {
        return $this->date_depart;
    }

    public function setDateDepart(?\DateTimeInterface $date_depart): static
    {
        $this->date_depart = $date_depart;

        return $this;
    }

    public function getDateArrivee(): ?\DateTimeInterface
    {
        return $this->date_arrivee;
    }

    public function setDateArrivee(?\DateTimeInterface $date_arrivee): static
    {
        $this->date_arrivee = $date_arrivee;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(?int $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getMois(): ?int
    {
        return $this->mois;
    }

    public function setMois(?int $mois): static
    {
        $this->mois = $mois;

        return $this;
    }

    public function isConfirmationUsine(): ?bool
    {
        return $this->confirmation_usine;
    }

    public function setConfirmationUsine(?bool $confirmation_usine): static
    {
        $this->confirmation_usine = $confirmation_usine;

        return $this;
    }

    public function getCodeDocbtgu(): ?Documentbtgu
    {
        return $this->code_docbtgu;
    }

    public function setCodeDocbtgu(?Documentbtgu $code_docbtgu): static
    {
        $this->code_docbtgu = $code_docbtgu;

        return $this;
    }

    /**
     * @return Collection<int, Lignepagebtgu>
     */
    public function getLignepagebtgus(): Collection
    {
        return $this->lignepagebtgus;
    }

    public function addLignepagebtgu(Lignepagebtgu $lignepagebtgu): static
    {
        if (!$this->lignepagebtgus->contains($lignepagebtgu)) {
            $this->lignepagebtgus->add($lignepagebtgu);
            $lignepagebtgu->setCodePagebtgu($this);
        }

        return $this;
    }

    public function removeLignepagebtgu(Lignepagebtgu $lignepagebtgu): static
    {
        if ($this->lignepagebtgus->removeElement($lignepagebtgu)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebtgu->getCodePagebtgu() === $this) {
                $lignepagebtgu->setCodePagebtgu(null);
            }
        }

        return $this;
    }


}
