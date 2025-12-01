<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\StoreRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StoreRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Store
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    #[Groups(['store:read', 'store:write'])]
    #[Assert\NotBlank(message: 'Name cannot be blank.', groups: ['store:write'])]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Name cannot be longer than {{ limit }} characters.',
        groups: ['store:write']
    )]
    private string $name;

    #[ORM\Column(length: 255)]
    #[Groups(['store:read', 'store:write'])]
    #[Assert\NotBlank(message: 'Address cannot be blank.', groups: ['store:write'])]
    private string $address;

    #[ORM\Column(length: 100)]
    #[Groups(['store:read', 'store:write'])]
    #[Assert\NotBlank(message: 'City cannot be blank.', groups: ['store:write'])]
    private string $city;

    #[ORM\Column(length: 10)]
    #[Groups(['store:read', 'store:write'])]
    #[Assert\NotBlank(message: 'Postal code cannot be blank.', groups: ['store:write'])]
    #[Assert\Length(
        max: 5,
        maxMessage: 'Postal code cannot be longer than {{ limit }} characters.',
        groups: ['store:write']
    )]
    private string $postalCode;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
