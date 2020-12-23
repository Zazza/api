<?php
namespace App\Service;

use App\Entity\Wallet;
use App\Exception\DbSaveException;
use App\Repository\WalletRepository;
use App\Wallet\Convert;
use App\Wallet\Currency;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Transaction
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var Convert
     */
    private $convert;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var WalletRepository
     */
    private $walletRepository;

    /**
     * @var Wallet
     */
    private $wallet;

    /**
     * @var \App\Entity\StaticCurrency|null
     */
    private $currencyEntity;

    /**
     * @var int
     */
    private $amount;

    public function __construct(
        ContainerInterface $container,
        WalletRepository $walletRepository,
        ValidatorInterface $validator,
        Currency $currency,
        Convert $convert
    )
    {
        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();
        $this->walletRepository = $walletRepository;
        $this->validator = $validator;
        $this->currency = $currency;
        $this->convert = $convert;
    }

    public function setWallet(int $id): self
    {
        $this->wallet = $this->walletRepository->find($id);

        return $this;
    }

    /**
     * @return Wallet
     */
    public function getWallet(): Wallet
    {
        return $this->wallet;
    }

    public function setCurrency(int $currencyId): self
    {
        $this->currencyEntity = $this->currency->getModel($currencyId);

        return $this;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount * Convert::CAST;

        return $this;
    }

    public function add(int $typeId, int $reasonId): self
    {
        $transaction = new \App\Entity\Transaction();
        $transaction->setWallet($this->wallet);
        $transaction->setCurrency($this->currencyEntity);
        $transaction->setTypeId($typeId);
        $transaction->setReasonId($reasonId);
        $transaction->setAmount($this->amount);

        $errors = $this->validator->validate($transaction);
        if (count($errors) > 0) {
            $errorList = [];
            foreach ($errors as $index=>$error) {
                $errorList[] = $error->getMessage();
            }
            throw new DbSaveException(implode(', ', $errorList));
        }

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        return $this;
    }

    public function walletAmount()
    {
        $this->wallet->setAmount(
            $this->wallet->getAmount()
            + $this->convert->setWallet($this->wallet)->calculate($this->currencyEntity->getId(), $this->amount)
        );

        $this->entityManager->persist($this->wallet);
        $this->entityManager->flush();
    }
}
