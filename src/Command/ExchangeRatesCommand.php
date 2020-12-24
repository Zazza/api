<?php
namespace App\Command;

use App\Entity\CurrencyExchangeRate;
use App\Entity\StaticCurrency;
use App\Wallet\ExchangeRate\Exchange;
use App\Wallet\ExchangeRate\Guzzle;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ExchangeRatesCommand
 * Update exchange rate
 * @package App\Command
 */
class ExchangeRatesCommand extends Command
{
    protected static $defaultName = 'app:exchange_rates';

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();
    }

    protected function configure()
    {
        $this
            ->setDescription('Exchange rates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = new Exchange(
            new Guzzle()
        );

        $currencyRepository = $this->entityManager->getRepository(StaticCurrency::class);
        $currencyList = $currencyRepository->findAll();

        /** @var StaticCurrency $currency */
        foreach ($currencyList as $currency) {
            if ($currency->isMain()) continue;
            $rate = $source->request($currency->getName());

            $exchangeRate = new CurrencyExchangeRate();
            $exchangeRate->setCurrencyId($currency->getId());
            $exchangeRate->setValue($rate);

            $this->entityManager->persist($exchangeRate);
            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
