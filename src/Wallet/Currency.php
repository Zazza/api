<?php
namespace App\Wallet;

use App\Entity\StaticCurrency;
use App\Repository\CurrencyExchangeRateRepository;
use App\Repository\StaticCurrencyRepository;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Currency
{
    /**
     * @var StaticCurrencyRepository
     */
    private $currencyRepository;

    public function __construct(ContainerInterface $container)
    {
        $entityManager = $container->get('doctrine')->getManager();
        $this->currencyRepository = $entityManager->getRepository(StaticCurrency::class);
    }

    public function getModel(int $currencyId): ?StaticCurrency
    {
        return $this->currencyRepository->find($currencyId);
    }
}
