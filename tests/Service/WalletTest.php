<?php
namespace App\Tests\Service;

use App\Entity\StaticCurrency;
use App\Entity\Wallet;
use App\Exception\CurrencyNotFoundException;
use App\Exception\WalletNotFoundException;
use App\Service\Wallet as WalletService;
use App\Wallet\Convert;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WalletTest extends WebTestCase
{
    /**
     * @var WalletService
     */
    private $walletService;

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
        $this->walletService = self::$container->get(WalletService::class);

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

        $this->walletService
            ->setWallet(5)
            ->setCurrency($this->currencies[0]->getName())
            ->setAmount(1)
            ->updateBalance();
    }

    public function testCurrencyNotFoundException(): void
    {
        $this->expectException(CurrencyNotFoundException::class);

        $this->walletService
            ->setWallet($this->wallets[0]->getId())
            ->setCurrency('EUR')
            ->setAmount(1)
            ->updateBalance();
    }

    public function testSuccessAdd(): void
    {
        self::bootKernel();
        $this->walletService = self::$container->get(WalletService::class);

        $this->walletService
            ->setWallet($this->wallets[0]->getId())
            ->setCurrency($this->currencies[0]->getName())
            ->setAmount($amount = 1)
            ->updateBalance();

        $walletRepository = $this->entityManager->getRepository(Wallet::class);
        /** @var Wallet $lastRecord */
        $wallet = $walletRepository->find($this->wallets[0]->getId());

        self::assertEquals($wallet->getId(), $this->wallets[0]->getId());
        self::assertNotEquals($wallet->getAmount(), $amount * Convert::CAST);
    }
}
