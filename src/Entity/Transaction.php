<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StaticTransactionReason", inversedBy="reasons")
     * @Assert\NotNull(message="Choose a valid transaction reason [stock|refund].")
     */
    private $reason;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StaticTransactionType", inversedBy="types")
     * @Assert\NotNull(message="Choose a valid transaction type [debit|credit].")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StaticCurrency", inversedBy="currencies")
     * @Assert\NotNull(message="Choose a valid currency type.")
     */
    private $currency;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Wallet", inversedBy="wallets", cascade={"persist"})
     */
    private $wallet;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $created_at;

    /**
     * Transaction constructor.
     */
    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReason(): StaticTransactionReason
    {
        return $this->reason;
    }

    public function setReason(StaticTransactionReason $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getType(): StaticTransactionType
    {
        return $this->type;
    }

    public function setType(StaticTransactionType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCurrency(): StaticCurrency
    {
        return $this->currency;
    }

    public function setCurrency(StaticCurrency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getWallet(): Wallet
    {
        return $this->wallet;
    }

    public function setWallet(Wallet $wallet): self
    {
        $this->wallet = $wallet;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
