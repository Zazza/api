<?php

namespace App\Entity;

use App\Repository\CurrencyExchangeRateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CurrencyExchangeRateRepository::class)
 */
class CurrencyExchangeRate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     */
    private $currency_id;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $value;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $created_at;

    /**
     * CurrencyExchangeRate constructor.
     */
    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrencyId(): int
    {
        return $this->currency_id;
    }

    public function setCurrencyId(int $currencyId): self
    {
        $this->currency_id = $currencyId;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
