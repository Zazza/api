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
    const TRANSACTION_TYPE_DEBIT = 1;
    const TRANSACTION_TYPE_CREDIT = 2;

    const TRANSACTION_TYPES = [self::TRANSACTION_TYPE_DEBIT, self::TRANSACTION_TYPE_CREDIT];

    const TRANSACTION_REASON_STOCK = 1;
    const TRANSACTION_REASON_REFUND = 2;

    const TRANSACTION_REASONS = [self::TRANSACTION_REASON_STOCK, self::TRANSACTION_REASON_REFUND];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Choice(choices=Transaction::TRANSACTION_REASONS, message="Choose a valid transation reason.")
     */
    private $reason_id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Choice(choices=Transaction::TRANSACTION_TYPES, message="Choose a valid transation type.")
     */
    private $type_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StaticCurrency", inversedBy="currencies")
     * @Assert\NotNull(message="Choose a valid currency type.")
     */
    private $currency;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Wallet", inversedBy="wallets")
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

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReasonId(): ?int
    {
        return $this->reason_id;
    }

    public function setReasonId(int $reason_id): self
    {
        $this->reason_id = $reason_id;

        return $this;
    }

    public function getTypeId(): ?int
    {
        return $this->type_id;
    }

    public function setTypeId(int $type_id): self
    {
        $this->type_id = $type_id;

        return $this;
    }

    public function getCurrency(): StaticCurrency
    {
        return $this->currency;
    }

    public function setCurrency(?StaticCurrency $currency): self
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
