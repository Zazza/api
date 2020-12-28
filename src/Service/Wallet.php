<?php
namespace App\Service;

use App\Entity\Transaction;
use App\Exception\DbSaveException;
use App\Exception\ExchangeRateException;
use App\Exception\TransactionReasonNotFoundException;
use App\Exception\TransactionTypeNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Class Wallet
 * Wallet Service
 * @package App\Service
 */
class Wallet extends Common
{
    /**
     * Update user wallet balance
     * @throws DbSaveException
     * @throws ExchangeRateException
     * @throws ORMException
     */
    public function updateBalance(): void
    {
        $newBalance = $this->wallet->getAmount()
            + $this->convert
                ->setWalletCurrency($this->wallet->getCurrency())
                ->setConvertCurrency($this->currencyEntity)
                ->calculate($this->amount);

        $this->wallet->setAmount($newBalance);

        $errors = $this->validator->validate($this->wallet);
        if (count($errors) > 0) {
            $errorList = [];
            foreach ($errors as $index=>$error) {
                $errorList[] = $error->getMessage();
            }

            throw new DbSaveException(implode(' ', $errorList));
        }

        $this->entityManager->persist($this->wallet);
        $this->entityManager->flush();
    }

    /**
     * Added new transaction
     * @param string $type
     * @param string $reason
     * @throws DbSaveException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionTypeNotFoundException
     * @throws TransactionReasonNotFoundException
     */
    public function addTransaction(string $type, string $reason): void
    {
        $transaction = new Transaction();
        $transaction->setWallet($this->wallet);
        $transaction->setCurrency($this->currencyEntity);
        $transaction->setType($this->getStaticTransactionType($type));
        $transaction->setReason($this->getStaticTransactionReason($reason));
        $transaction->setAmount($this->amount);

        $errors = $this->validator->validate($transaction);
        if (count($errors) > 0) {
            $errorList = [];
            foreach ($errors as $index=>$error) {
                $errorList[] = $error->getMessage();
            }

            throw new DbSaveException(implode(' ', $errorList));
        }

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();
    }
}
