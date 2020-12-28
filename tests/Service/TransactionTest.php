<?php
namespace App\Tests\Service;

use App\Entity\StaticCurrency;
use App\Entity\StaticTransactionReason;
use App\Entity\StaticTransactionType;
use App\Entity\Transaction;
use App\Entity\Wallet;
use App\Exception\CurrencyNotFoundException;
use App\Exception\DbSaveException;
use App\Exception\TransactionReasonNotFoundException;
use App\Exception\TransactionTypeNotFoundException;
use App\Exception\WalletNotFoundException;
use App\Service\Wallet as WalletService;
use App\Wallet\Convert;
use App\Wallet\TransactionReason;
use App\Wallet\TransactionType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TransactionTest extends WebTestCase
{
    /**
     * @var WalletService
     */
    private $transactionService;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var array
     */
    private $currencies;

    /**
     * @var array
     */
    private $transactionReason;

    /**
     * @var array
     */
    private $transactionType;

    /**
     * @var array
     */
    private $wallets;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        self::bootKernel();
        $container = self::$kernel->getContainer();
        $this->transactionService = self::$container->get(WalletService::class);

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $staticCurrencyRepository = $this->entityManager->getRepository(StaticCurrency::class);
        $this->currencies = $staticCurrencyRepository->findAll();

        $staticTransactionReason = $this->entityManager->getRepository(StaticTransactionReason::class);
        $this->transactionReason = $staticTransactionReason->findAll();

        $staticTransactionType = $this->entityManager->getRepository(StaticTransactionType::class);
        $this->transactionType = $staticTransactionType->findAll();

        $walletRepository = $this->entityManager->getRepository(Wallet::class);
        $this->wallets = $walletRepository->findAll();
    }

    public function testWalletNotFoundException(): void
    {
        $this->expectException(WalletNotFoundException::class);

        $this->transactionService
            ->setWallet(5)
            ->setCurrency($this->currencies[0]->getName())
            ->setAmount(1)
            ->addTransaction($this->transactionType[0]->getName(), $this->transactionReason[0]->getName());
    }

    public function testCurrencyNotFoundException(): void
    {
        $this->expectException(CurrencyNotFoundException::class);

        $this->transactionService
            ->setWallet($this->wallets[0]->getId())
            ->setCurrency('EUR')
            ->setAmount(1)
            ->addTransaction($this->transactionType[0]->getName(), $this->transactionReason[0]->getName());
    }

    public function testTxTypeDbSaveException(): void
    {
        $this->expectException(TransactionTypeNotFoundException::class);
        $this->expectErrorMessage('No transaction type found: balance');

        $this->transactionService
            ->setWallet($this->wallets[0]->getId())
            ->setCurrency($this->currencies[0]->getName())
            ->setAmount(1)
            ->addTransaction('balance', $this->transactionReason[0]->getName());
    }

    public function testTxReasonDbSaveException(): void
    {
        $this->expectException(TransactionReasonNotFoundException::class);
        $this->expectErrorMessage('No transaction reason found: balance');

        $this->transactionService
            ->setWallet($this->wallets[0]->getId())
            ->setCurrency($this->currencies[0]->getName())
            ->setAmount(1)
            ->addTransaction($this->transactionType[0]->getName(), 'balance');
    }

    public function testSuccessAdd(): void
    {
        self::bootKernel();
        $this->transactionService = self::$container->get(WalletService::class);

        $this->transactionService
            ->setWallet($this->wallets[0]->getId())
            ->setCurrency($this->currencies[0]->getName())
            ->setAmount($amount = 1)
            ->addTransaction($this->transactionType[0]->getName(), $this->transactionReason[0]->getName());

        $transactionRepository = $this->entityManager->getRepository(Transaction::class);
        /** @var Transaction $lastRecord */
        $lastRecord = $transactionRepository->findOneBy([], ['id'=> 'DESC']);

        self::assertEquals($lastRecord->getCurrency()->getId(), $this->currencies[0]->getId());
        self::assertEquals($lastRecord->getWallet()->getId(), $this->wallets[0]->getId());
        self::assertEquals($lastRecord->getType()->getId(), $this->transactionType[0]->getId());
        self::assertEquals($lastRecord->getReason()->getId(), $this->transactionReason[0]->getId());
        self::assertEquals($lastRecord->getAmount(), $amount * Convert::CAST);
    }
}
