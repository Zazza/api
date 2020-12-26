<?php

namespace App\Controller;

use App\Exception\DbSaveException;
use App\Exception\ExchangeRateException;
use App\Exception\WalletNotFoundException;
use App\Exception\CurrencyNotFoundException;
use \Doctrine\DBAL\ConnectionException;
use App\Service\Wallet as WalletService;
use App\Wallet\Convert;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiWalletController extends AbstractController
{
    /**
     * @Route("/api/wallet/getBalance/{id}", methods={"GET"}, name="api_wallet_get_balance")
     * @param int $id
     * @param WalletService $walletService
     * @return Response
     * @throws WalletNotFoundException
     */
    public function getBalance(int $id, WalletService $walletService): Response
    {
        $walletService->setWallet($id);
        $wallet = $walletService->getWallet();

        return $this->json([
            'currency' => $wallet->getCurrency()->getName(),
            'amount' => (string)($wallet->getAmount() / Convert::CAST)
        ]);
    }

    /**
     * @Route("/api/wallet/updateBalance/{id}", methods={"POST"}, name="api_wallet_update_balance")
     * @param Request $request
     * @param int $id
     * @param WalletService $walletService
     * @return Response
     * @throws WalletNotFoundException
     * @throws CurrencyNotFoundException
     */
    public function updateBalance(
        Request $request,
        int $id,
        WalletService $walletService
    ): Response
    {
        $walletService->setWallet($id);

        $walletService->beginTransaction();

        $walletService
            ->setCurrency($request->request->get('currency', ''))
            ->setAmount($request->get('amount', 0));

        try {
            $walletService->addTransaction(
                $request->get('type', false),
                $request->get('reason', false)
            );
            $walletService->updateBalance();
            $walletService->commitTransaction();
        } catch (\Throwable $e) {
            $walletService->rollbackTransaction();

            throw $e;
        }

        return $this->json([
            'result' => true
        ]);
    }
}
