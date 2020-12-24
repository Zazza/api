<?php
namespace App\Tests\Wallet;

use App\Entity\CurrencyExchangeRate;
use App\Entity\StaticCurrency;
use App\Wallet\Convert;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConvertTest extends WebTestCase
{
    public const AMOUNT = 1; // 1 USR or 1 RUB

    /**
     * @var array
     */
    private $currencies;

    /**
     * @var Convert
     */
    private $convert;

    /** @var string */
    private $exchangeRate;

    /**
     * @var int
     */
    private $amount;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        self::bootKernel();
        $container = self::$kernel->getContainer();

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $staticCurrencyRepository = $this->entityManager->getRepository(StaticCurrency::class);
        $this->currencies = $staticCurrencyRepository->findAll();

        /** @var Convert $convert */
        $this->convert = self::$container->get(Convert::class);

        $exchangeRateRepository = $this->entityManager->getRepository(CurrencyExchangeRate::class);
        $this->exchangeRate = $exchangeRateRepository->findOneBy([], ['id'=> 'DESC'])->getValue();

        $this->amount = self::AMOUNT * Convert::CAST;
    }

    public function testCalculateOne(): void
    {
        $convertedAmount = $this->convert
            ->setWalletCurrency($this->currencies[0])
            ->setConvertCurrency($this->currencies[1])
            ->calculate($this->amount);

        self::assertEquals($convertedAmount, round($this->amount / $this->exchangeRate));
    }

    public function testCalculateTwo(): void
    {
        $convertedAmount = $this->convert
            ->setWalletCurrency($this->currencies[1])
            ->setConvertCurrency($this->currencies[0])
            ->calculate($this->amount);

        self::assertEquals($convertedAmount, round($this->amount * $this->exchangeRate));
    }
}
