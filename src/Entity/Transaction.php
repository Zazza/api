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
    public const TRANSACTION_TYPE_DEBIT = 'debit';
    public const TRANSACTION_TYPE_CREDIT = 'credit';

    public const TRANSACTION_TYPES = [self::TRANSACTION_TYPE_DEBIT, self::TRANSACTION_TYPE_CREDIT];

    public const TRANSACTION_REASON_STOCK = 'stock';
    public const TRANSACTION_REASON_REFUND = 'refund';

    public const TRANSACTION_REASONS = [self::TRANSACTION_REASON_STOCK, self::TRANSACTION_REASON_REFUND];

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
    private $reason_id = 0;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Choice(callback="getTypes", message="Choose a valid transaction type [debit|credit].")
     */
    private $type_id = 0;

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
        $index = array_search($reason,self::TRANSACTION_REASONS);
        if (false !== $index) {
            $this->reason_id = $index+1;
        }

        return $this;
    }

    public function getTypeId(): int
    {
        return $this->type_id;
    }

    public function setType(string $type): self
    {
        $index = array_search($type,self::TRANSACTION_TYPES);
        if (false !== $index) {
            $this->type_id = $index+1;
        }

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

    public static function getReasons()
    {
        return [1, count(self::TRANSACTION_REASONS)];
    }

    public static function getTypes()
    {
        return [1, count(self::TRANSACTION_TYPES)];
    }
}
