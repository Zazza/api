<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Wallet\TransactionType;
use App\Wallet\TransactionReason;

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
     * @ORM\Column(type="integer")
     * @Assert\Choice(callback="getReasons", message="Choose a valid transaction reason [stock|refund].")
     */
    private $reason_id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Choice(callback="getTypes", message="Choose a valid transaction type [debit|credit].")
     */
    private $type_id;

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

    public function getReasonId(): int
    {
        return $this->reason_id;
    }

    public function setReason(string $reason): self
    {
        $this->reason_id = array_search($reason,TransactionReason::VALUES);

        return $this;
    }

    public function getTypeId(): int
    {
        return $this->type_id;
    }

    public function setType(string $type): self
    {
        $this->type_id = array_search($type,TransactionType::VALUES);

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

    public static function getReasons()
    {
        return array_keys(TransactionType::VALUES);
    }

    public static function getTypes()
    {
        return array_keys(TransactionReason::VALUES);
    }
}
