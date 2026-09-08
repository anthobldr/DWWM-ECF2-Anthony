<?php

namespace App\Entity;

use App\Repository\AbsenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbsenceRepository::class)]
class Absence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'absences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trainee $trainee_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTraineeId(): ?Trainee
    {
        return $this->trainee_id;
    }

    public function setTraineeId(?Trainee $trainee_id): static
    {
        $this->trainee_id = $trainee_id;

        return $this;
    }
}
