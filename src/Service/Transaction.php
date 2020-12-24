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
     * @param int $typeId
     * @param int $reasonId
     * @throws DbSaveException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(int $typeId, int $reasonId): void
    {
        //$this->entityManager->getConnection()->beginTransaction();

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

            //$this->entityManager->getConnection()->rollBack();
            throw new DbSaveException(implode(' ', $errorList));
        }

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        //$this->entityManager->getConnection()->commit();
    }
}
