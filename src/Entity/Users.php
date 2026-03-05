<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[UniqueEntity(fields: ["username"], message: "le username doit être unique", groups: ["registration"])]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 191, unique: true)]
    #[Assert\NotBlank(message: "Veuillez encoder un username", groups: ["registration"])]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "veuillez encoder un mot de passe", groups: ["registration"])]
    #[Assert\Regex(
        pattern: "/^(?=.*[A-Z])(?=.*[!@#$%^&*]).*$/",
        message: "le password doit contenir au moins 1 majuscule, 1 caractère special",
        groups: ["registration"]
    )]
    #[Assert\Length(
        min: 8,
        minMessage: "la longueur doit être d'au moins 8 caractères",
        groups: ["registration"]
    )]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email(message: "email doit être valide", groups: ["Default", "registration"])]
    private ?string $mail = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Role $role = null;

    // Champ NON mappé en DB (sert uniquement à confirmer le mot de passe)
    #[Assert\EqualTo(
        propertyPath: "password",
        message: "les mots de passe ne correspondent pas",
        groups: ["registration"]
    )]
    private ?string $confirm_password = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): static
    {
        $this->mail = $mail;
        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirm_password;
    }

    public function setConfirmPassword(?string $confirm_password): static
    {
        $this->confirm_password = $confirm_password;
        return $this;
    }
}