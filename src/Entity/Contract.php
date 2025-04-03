<?php

namespace App\Entity;

use App\Repository\ContractRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContractRepository::class)]
class Contract
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'contracts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employee $employee = null;

    #[ORM\ManyToOne(inversedBy: 'contracts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Title $title = null;

    #[ORM\Column]
    private ?int $salaryInCents = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $salary = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $beginDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Target $currentTarget = null;

    /**
     * @var Collection<int, Target>
     */
    #[ORM\OneToMany(targetEntity: Target::class, mappedBy: 'contract', orphanRemoval: true)]
    private Collection $targets;

    public function __construct()
    {
        $this->targets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    public function getTitle(): ?Title
    {
        return $this->title;
    }

    public function setTitle(?Title $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSalaryInCents(): ?int
    {
        return $this->salaryInCents;
    }

    public function setSalaryInCents(int $salaryInCents): static
    {
        $this->salaryInCents = $salaryInCents;

        return $this;
    }

    public function getSalary(): ?string
    {
        return $this->salary;
    }

    public function setSalary(string $salary): static
    {
        $this->salary = $salary;

        return $this;
    }

    public function getBeginDate(): ?\DateTimeImmutable
    {
        return $this->beginDate;
    }

    public function setBeginDate(\DateTimeImmutable $beginDate): static
    {
        $this->beginDate = $beginDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCurrentTarget(): ?Target
    {
        return $this->currentTarget;
    }

    public function setCurrentTarget(?Target $currentTarget): static
    {
        $this->currentTarget = $currentTarget;

        return $this;
    }

    /**
     * @return Collection<int, Target>
     */
    public function getTargets(): Collection
    {
        return $this->targets;
    }

    public function addTarget(Target $target): static
    {
        if (!$this->targets->contains($target)) {
            $this->targets->add($target);
            $target->setContract($this);
        }

        return $this;
    }

    public function removeTarget(Target $target): static
    {
        if ($this->targets->removeElement($target)) {
            // set the owning side to null (unless already changed)
            if ($target->getContract() === $this) {
                $target->setContract(null);
            }
        }

        return $this;
    }
}
