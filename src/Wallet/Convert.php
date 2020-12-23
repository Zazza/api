<?php
namespace App\Wallet;

use App\Entity\CurrencyExchangeRate;
use App\Entity\Wallet;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Convert
{
    const CAST = 100;

    /**
     * @var Wallet
     */
    private $wallet;

    /**
     * @var ObjectRepository
     */
    private $exchangeRateRepository;

    public function __construct(ContainerInterface $container)
    {
        $entityManager = $container->get('doctrine')->getManager();
        $this->exchangeRateRepository = $entityManager->getRepository(CurrencyExchangeRate::class);
    }

    /**
     * @param Wallet $wallet
     * @return $this
     */
    public function setWallet(Wallet $wallet): self
    {
        $this->wallet = $wallet;

        return $this;
    }

    public function calculate(int $currencyId, int $amount): int
    {
        if ($currencyId === $this->wallet->getCurrency()->getId()) {
            return $amount;
        }

        if ($this->wallet->getCurrency()->isMain()) {
            /** @var CurrencyExchangeRate $exchangeRate */
            $exchangeRate = $this->exchangeRateRepository->findBy(
                ['currency_id' => $currencyId],
                ['id' => 'DESC'],
                1, 0);
            $rate = $exchangeRate[0]->getValue();

            $convert = $amount * $rate;
        } else {
            /** @var CurrencyExchangeRate $exchangeRate */
            $exchangeRate = $this->exchangeRateRepository->findBy(
                ['currency_id' => $this->wallet->getCurrency()->getId()],
                ['id' => 'DESC'],
                1, 0);
            $rate = $exchangeRate[0]->getValue();

            $convert = $amount / $rate;
        }

        return $convert;
    }
}
