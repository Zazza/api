<?php
namespace App\Wallet;

use App\Entity\CurrencyExchangeRate;
use App\Entity\StaticCurrency;
use App\Exception\ExchangeRateException;
use App\Repository\CurrencyExchangeRateRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Convert
 * @package App\Wallet
 */
class Convert
{
    const CAST = 100;

    /**
     * @var CurrencyExchangeRateRepository
     */
    private $exchangeRateRepository;

    /**
     * @var StaticCurrency
     */
    private StaticCurrency $walletCurrency;

    /**
     * @var StaticCurrency
     */
    private StaticCurrency $convertCurrency;

    /**
     * Convert constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $entityManager = $container->get('doctrine')->getManager();
        $this->exchangeRateRepository = $entityManager->getRepository(CurrencyExchangeRate::class);
    }

    /**
     * @param StaticCurrency $currency
     * @return Convert
     */
    public function setWalletCurrency(StaticCurrency $currency): Convert
    {
        $this->walletCurrency = $currency;
        return $this;
    }

    /**
     * @param StaticCurrency $currency
     * @return Convert
     */
    public function setConvertCurrency(StaticCurrency $currency): Convert
    {
        $this->convertCurrency = $currency;
        return $this;
    }

    /**
     * Convert amount from other currency
     * @param int $amount
     * @return int
     * @throws ExchangeRateException
     */
    public function calculate(int $amount): int
    {
        if ($this->convertCurrency === $this->walletCurrency) {
            return $amount;
        }

        if ($this->walletCurrency->isMain()) {
            /** @var CurrencyExchangeRate $exchangeRate */
            $exchangeRate = $this->exchangeRateRepository->findBy(
                ['currency_id' => $this->convertCurrency->getId()],
                ['id' => 'DESC'],
                1, 0
            );
            if (!$exchangeRate) {
                throw new ExchangeRateException($this->convertCurrency->getName());
            }
            $rate = $exchangeRate[0]->getValue();

            $convert = $amount * $rate;
        } else {
            /** @var CurrencyExchangeRate $exchangeRate */
            $exchangeRate = $this->exchangeRateRepository->findBy(
                ['currency_id' => $this->walletCurrency->getId()],
                ['id' => 'DESC'],
                1, 0
            );
            if (!$exchangeRate) {
                throw new ExchangeRateException($this->walletCurrency->getName());
            }
            $rate = $exchangeRate[0]->getValue();

            $convert = $amount / $rate;
        }

        return (int) round($convert);
    }
}
