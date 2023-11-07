<?php

namespace App\Entity;

use App\Repository\EmployerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployerRepository::class)]
class Employer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'employers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    #[ORM\OneToMany(mappedBy: 'employer', targetEntity: Vacancy::class, orphanRemoval: true)]
    private Collection $vacancies;

    public function __construct()
    {
        $this->vacancies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection<int, Vacancy>
     */
    public function getVacancies(): Collection
    {
        return $this->vacancies;
    }

    public function addVacancy(Vacancy $vacancy): static
    {
        if (!$this->vacancies->contains($vacancy)) {
            $this->vacancies->add($vacancy);
            $vacancy->setEmployer($this);
        }

        return $this;
    }

    public function removeVacancy(Vacancy $vacancy): static
    {
        if ($this->vacancies->removeElement($vacancy)) {
            // set the owning side to null (unless already changed)
            if ($vacancy->getEmployer() === $this) {
                $vacancy->setEmployer(null);
            }
        }

        return $this;
    }
}
