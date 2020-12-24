<?php
namespace App\Tests\Service;

use App\Entity\StaticCurrency;
use App\Entity\Transaction;
use App\Entity\Wallet;
use App\Exception\CurrencyNotFoundException;
use App\Exception\DbSaveException;
use App\Exception\WalletNotFoundException;
use App\Service\Transaction as TransactionService;
use App\Wallet\Convert;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TransactionTest extends WebTestCase
{
    /**
     * @var TransactionService
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
        $this->transactionService = self::$container->get(TransactionService::class);

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
            ->add(Transaction::TRANSACTION_TYPE_DEBIT, Transaction::TRANSACTION_REASON_STOCK);
    }

    public function testCurrencyNotFoundException(): void
    {
        $this->expectException(CurrencyNotFoundException::class);

        $this->transactionService
            ->setWallet($this->wallets[0]->getId())
            ->setCurrency('EUR')
            ->setAmount(1)
            ->add(Transaction::TRANSACTION_TYPE_DEBIT, Transaction::TRANSACTION_REASON_STOCK);
    }

    public function testTxTypeDbSaveException(): void
    {
        $this->expectException(DbSaveException::class);
        $this->expectErrorMessage('Choose a valid transaction type [debit|credit].');

        $this->transactionService
            ->setWallet($this->wallets[0]->getId())
            ->setCurrency($this->currencies[0]->getName())
            ->setAmount(1)
            ->add('balance', Transaction::TRANSACTION_REASON_STOCK);
    }

    public function testTxReasonDbSaveException(): void
    {
        $this->expectException(DbSaveException::class);
        $this->expectErrorMessage('Choose a valid transaction reason [stock|refund].');

        $this->transactionService
            ->setWallet($this->wallets[0]->getId())
            ->setCurrency($this->currencies[0]->getName())
            ->setAmount(1)
            ->add(Transaction::TRANSACTION_TYPE_CREDIT, 'balance');
    }

    public function testSuccessAdd(): void
    {
        self::bootKernel();
        $this->transactionService = self::$container->get(TransactionService::class);

        $this->transactionService
            ->setWallet($this->wallets[0]->getId())
            ->setCurrency($this->currencies[0]->getName())
            ->setAmount($amount = 1)
            ->add(Transaction::TRANSACTION_TYPE_DEBIT, Transaction::TRANSACTION_REASON_STOCK);

        $transactionRepository = $this->entityManager->getRepository(Transaction::class);
        /** @var Transaction $lastRecord */
        $lastRecord = $transactionRepository->findOneBy([], ['id'=> 'DESC']);

        self::assertEquals($lastRecord->getCurrency()->getId(), $this->currencies[0]->getId());
        self::assertEquals($lastRecord->getWallet()->getId(), $this->wallets[0]->getId());
        self::assertEquals($lastRecord->getTypeId(),
            array_search(Transaction::TRANSACTION_TYPE_DEBIT,Transaction::TRANSACTION_TYPES)+1);
        self::assertEquals($lastRecord->getReasonId(),
            array_search(Transaction::TRANSACTION_REASON_STOCK,Transaction::TRANSACTION_REASONS)+1);
        self::assertEquals($lastRecord->getAmount(), $amount * Convert::CAST);
    }
}
