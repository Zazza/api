<?php
namespace App\Service;

use App\Exception\DbSaveException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Class Transaction
 * Transaction service
 * @package App\Service
 */
class Transaction extends Common
{
    /**
     * Added new transaction
     * @param string $type
     * @param string $reason
     * @throws DbSaveException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(string $type, string $reason): void
    {
        $transaction = new \App\Entity\Transaction();
        $transaction->setWallet($this->wallet);
        $transaction->setCurrency($this->currencyEntity);
        $transaction->setType($type);
        $transaction->setReason($reason);
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
