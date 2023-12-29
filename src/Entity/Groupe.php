<?php

namespace App\Entity;

use App\Repository\GroupeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.groupe')]
#[ORM\Entity(repositoryClass: GroupeRepository::class)]
class Groupe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom_groupe = null;

    #[ORM\OneToMany(mappedBy: 'code_groupe', targetEntity: Permission::class)]
    private Collection $permissions;

    #[ORM\OneToMany(mappedBy: 'code_groupe', targetEntity: User::class)]
    private Collection $utilisateurs;

    #[ORM\Column(nullable: true)]
    private ?bool $groupe_system = null;

    #[ORM\Column(nullable: true)]
    private ?int $parent_groupe = null;

    #[ORM\Column(nullable: true)]
    private ?int $code_type_operateur = null;

    #[ORM\Column(nullable: true)]
    private ?int $code_operateur = null;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
        $this->utilisateurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomGroupe(): ?string
    {
        return $this->nom_groupe;
    }

    public function setNomGroupe(string $nom_groupe): static
    {
        $this->nom_groupe = $nom_groupe;

        return $this;
    }

    /**
     * @return Collection<int, Permission>
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): static
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
            $permission->setCodeGroupe($this);
        }

        return $this;
    }

    public function removePermission(Permission $permission): static
    {
        if ($this->permissions->removeElement($permission)) {
            // set the owning side to null (unless already changed)
            if ($permission->getCodeGroupe() === $this) {
                $permission->setCodeGroupe(null);
            }
        }

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
            $utilisateur->setCodeGroupe($this);
        }

        return $this;
    }

    public function removeUser(User $utilisateur): static
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getCodeGroupe() === $this) {
                $utilisateur->setCodeGroupe(null);
            }
        }

        return $this;
    }

    public function isGroupeSystem(): ?bool
    {
        return $this->groupe_system;
    }

    public function setGroupeSystem(?bool $groupe_system): static
    {
        $this->groupe_system = $groupe_system;

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom_groupe;
    }

    public function getParentGroupe(): ?int
    {
        return $this->parent_groupe;
    }

    public function setParentGroupe(?int $parent_groupe): static
    {
        $this->parent_groupe = $parent_groupe;

        return $this;
    }

    public function getCodeTypeOperateur(): ?int
    {
        return $this->code_type_operateur;
    }

    public function setCodeTypeOperateur(?int $code_type_operateur): static
    {
        $this->code_type_operateur = $code_type_operateur;

        return $this;
    }

    public function getCodeOperateur(): ?int
    {
        return $this->code_operateur;
    }

    public function setCodeOperateur(?int $code_operateur): static
    {
        $this->code_operateur = $code_operateur;

        return $this;
    }
}
