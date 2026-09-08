<?php

namespace App\Entity;

use App\Repository\AbsenceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AbsenceRepository::class)]
class Absence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Relation ManyToOne avec Trainee
     */
    #[ORM\ManyToOne(targetEntity: Trainee::class, inversedBy: 'absences')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: 'Le stagiaire est requis')]
    private ?Trainee $trainee = null;

    /**
     * Date de l'absence
     */
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'La date est requise')]
    private ?\DateTime $date = null;

    /**
     * Raison de l'absence : 'maladie', 'sans_motif', 'legale', 'accident'
     */
    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'La raison est requise')]
    #[Assert\Choice(choices: ['maladie', 'sans_motif', 'legale', 'accident'])]
    private ?string $reason = null;

    /**
     * Notes/commentaires supplémentaires
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    /**
     * Chemin du fichier justificatif PDF
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $justificativeFilename = null;

    /**
     * Date et heure de création de l'absence
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $createdAt = null;

    /**
     * Date et heure de modification
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le stagiaire associé
     */
    public function getTrainee(): ?Trainee
    {
        return $this->trainee;
    }

    /**
     * Définit le stagiaire associé
     */
    public function setTrainee(?Trainee $trainee): static
    {
        $this->trainee = $trainee;
        return $this;
    }

    /**
     * Récupère la date de l'absence
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * Définit la date de l'absence
     */
    public function setDate(\DateTime $date): static
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Récupère la raison de l'absence
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * Définit la raison de l'absence
     */
    public function setReason(string $reason): static
    {
        $this->reason = $reason;
        return $this;
    }

    /**
     * Retourne le libellé français de la raison
     */
    public function getReasonLabel(): string
    {
        return match($this->reason) {
            'maladie' => '🤒 Maladie',
            'sans_motif' => '❓ Sans Motif',
            'legale' => '📜 Absence Légale',
            'accident' => '⚠️ Accident du Travail',
            default => 'Inconnu'
        };
    }

    /**
     * Vérifie si c'est une absence sans motif
     */
    public function isUnauthorized(): bool
    {
        return $this->reason === 'sans_motif';
    }

    /**
     * Récupère les notes
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * Définit les notes
     */
    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * Récupère le nom du fichier justificatif
     */
    public function getJustificativeFilename(): ?string
    {
        return $this->justificativeFilename;
    }

    /**
     * Définit le nom du fichier justificatif
     */
    public function setJustificativeFilename(?string $justificativeFilename): static
    {
        $this->justificativeFilename = $justificativeFilename;
        return $this;
    }

    /**
     * Récupère la date de création
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * Définit la date de création
     */
    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Récupère la date de modification
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Définit la date de modification
     */
    public function setUpdatedAt(?\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Appelé automatiquement avant un UPDATE
     */
    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }
}