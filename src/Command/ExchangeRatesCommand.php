<?php
namespace App\Command;

use App\Entity\CurrencyExchangeRate;
use App\Entity\StaticCurrency;
use App\Wallet\Exchange;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExchangeRatesCommand extends Command
{
    protected static $defaultName = 'app:exchange_rates';

    /**
     * @var
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
        $source = new Exchange();

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
