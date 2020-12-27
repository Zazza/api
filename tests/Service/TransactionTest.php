<?php
namespace App\Tests\Service;

use App\Entity\StaticCurrency;
use App\Entity\Transaction;
use App\Entity\Wallet;
use App\Exception\CurrencyNotFoundException;
use App\Exception\DbSaveException;
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
            ->addTransaction(TransactionType::VALUES[0], TransactionReason::VALUES[0]);
    }

    public function testCurrencyNotFoundException(): void
    {
        $this->expectException(CurrencyNotFoundException::class);

        $this->transactionService
            ->setWallet($this->wallets[0]->getId())
            ->setCurrency('EUR')
            ->setAmount(1)
            ->addTransaction(TransactionType::VALUES[0], TransactionReason::VALUES[0]);
    }

    public function testTxTypeDbSaveException(): void
    {
        $this->expectException(DbSaveException::class);
        $this->expectErrorMessage('Choose a valid transaction type [debit|credit].');

        $this->transactionService
            ->setWallet($this->wallets[0]->getId())
            ->setCurrency($this->currencies[0]->getName())
            ->setAmount(1)
            ->addTransaction('balance', TransactionReason::VALUES[0]);
    }

    public function testTxReasonDbSaveException(): void
    {
        $this->expectException(DbSaveException::class);
        $this->expectErrorMessage('Choose a valid transaction reason [stock|refund].');

        $this->transactionService
            ->setWallet($this->wallets[0]->getId())
            ->setCurrency($this->currencies[0]->getName())
            ->setAmount(1)
            ->addTransaction(TransactionType::VALUES[1], 'balance');
    }

    public function testSuccessAdd(): void
    {
        self::bootKernel();
        $this->transactionService = self::$container->get(WalletService::class);

        $this->transactionService
            ->setWallet($this->wallets[0]->getId())
            ->setCurrency($this->currencies[0]->getName())
            ->setAmount($amount = 1)
            ->addTransaction(TransactionType::VALUES[0], TransactionReason::VALUES[0]);

        $transactionRepository = $this->entityManager->getRepository(Transaction::class);
        /** @var Transaction $lastRecord */
        $lastRecord = $transactionRepository->findOneBy([], ['id'=> 'DESC']);

        self::assertEquals($lastRecord->getCurrency()->getId(), $this->currencies[0]->getId());
        self::assertEquals($lastRecord->getWallet()->getId(), $this->wallets[0]->getId());
        self::assertEquals($lastRecord->getTypeId(),
            array_search(TransactionType::VALUES[0],TransactionType::VALUES));
        self::assertEquals($lastRecord->getReasonId(),
            array_search(TransactionReason::VALUES[0],TransactionReason::VALUES));
        self::assertEquals($lastRecord->getAmount(), $amount * Convert::CAST);
    }
}
