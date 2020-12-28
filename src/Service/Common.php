<?php
namespace App\Service;

use App\Entity\StaticCurrency;
use App\Entity\StaticTransactionReason;
use App\Entity\StaticTransactionType;
use App\Entity\Wallet;
use App\Exception\CurrencyNotFoundException;
use App\Exception\TransactionReasonNotFoundException;
use App\Exception\TransactionTypeNotFoundException;
use App\Exception\WalletNotFoundException;
use App\Wallet\Convert;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class Common
 * @package App\Service
 */
class Common
{
    /**
     * @var ValidatorInterface
     */
    protected ValidatorInterface $validator;

    /**
     * @var Convert
     */
    protected Convert $convert;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Wallet|null
     */
    protected ?Wallet $wallet;

    /**
     * @var \App\Entity\StaticCurrency|null
     */
    protected ?StaticCurrency $currencyEntity;

    /**
     * @var int
     */
    protected int $amount;

    /**
     * Common constructor.
     * @param ContainerInterface $container
     * @param ValidatorInterface $validator
     * @param Convert $convert
     */
    public function __construct(
        ContainerInterface $container,
        ValidatorInterface $validator,
        Convert $convert
    )
    {
        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();
        $this->validator = $validator;
        $this->convert = $convert;
    }

    /**
     * @param int $id
     * @return $this|bool
     * @throws WalletNotFoundException
     */
    public function setWallet(int $id)
    {
        $walletRepository = $this->entityManager->getRepository(Wallet::class);
        $this->wallet = $walletRepository->find($id);
        if (!$this->wallet) {
            throw new WalletNotFoundException($id);
        }

        return $this;
    }

    /**
     * @return Wallet
     */
    public function getWallet(): Wallet
    {
        return $this->wallet;
    }

    /**
     * @param string $currency
     * @return $this
     * @throws CurrencyNotFoundException
     */
    public function setCurrency(string $currency): self
    {
        $currencyRepository = $this->entityManager->getRepository(StaticCurrency::class);
        $this->currencyEntity = $currencyRepository->findOneBy(['name' => $currency]);
        if (!$this->currencyEntity) {
            throw new CurrencyNotFoundException($currency);
        }

        return $this;
    }

    /**
     * @param string $type
     * @return StaticTransactionType
     * @throws TransactionTypeNotFoundException
     */
    public function getStaticTransactionType(string $type): StaticTransactionType
    {
        $repository = $this->entityManager->getRepository(StaticTransactionType::class);
        /** @var StaticTransactionType $transactionType */
        $transactionType = $repository->findOneBy(['name' => $type]);
        if (!$transactionType) {
            throw new TransactionTypeNotFoundException($type);
        }

        return $transactionType;
    }

    /**
     * @param string $reason
     * @return StaticTransactionReason
     * @throws TransactionReasonNotFoundException
     */
    public function getStaticTransactionReason(string $reason): StaticTransactionReason
    {
        $repository = $this->entityManager->getRepository(StaticTransactionReason::class);
        /** @var StaticTransactionReason $transactionReason */
        $transactionReason = $repository->findOneBy(['name' => $reason]);
        if (!$transactionReason) {
            throw new TransactionReasonNotFoundException($reason);
        }

        return $transactionReason;
    }

    /**
     * @param string $amount
     * @return $this
     */
    public function setAmount(string $amount): self
    {
        $this->amount = (int) (round($amount, 2) * Convert::CAST);

        return $this;
    }

    public function beginTransaction(): void
    {
        $this->entityManager->getConnection()->beginTransaction();
    }

    public function rollbackTransaction(): void
    {
        $this->entityManager->getConnection()->rollBack();
    }

    public function commitTransaction(): void
    {
        $this->entityManager->getConnection()->commit();
    }
}
