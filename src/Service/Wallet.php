<?php
namespace App\Service;

use App\Exception\DbSaveException;

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
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \App\Exception\ExchangeRateException
     */
    public function updateBalance(): void
    {
        $newBalance = $this->wallet->getAmount()
            + $this->convert
                ->setWalletCurrency($this->wallet->getCurrency())
                ->setConvertCurrency($this->currencyEntity)
                ->calculate($this->amount);

        $this->entityManager->getConnection()->beginTransaction();

        try {
            $this->wallet->setAmount($newBalance);

            $this->entityManager->persist($this->wallet);
            $this->entityManager->flush();

            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();

            throw new DbSaveException($e->getMessage());
        }
    }
}
