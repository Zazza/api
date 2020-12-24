<?php

namespace App\Controller;

use App\Exception\DbSaveException;
use App\Exception\ExchangeRateException;
use App\Exception\WalletNotFoundException;
use App\Exception\CurrencyNotFoundException;
use \Doctrine\DBAL\ConnectionException;
use App\Service\Wallet as WalletService;
use App\Service\Transaction as TransactionService;
use App\Wallet\Convert;
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
            'amount' => $wallet->getAmount() / Convert::CAST
        ]);
    }

    /**
     * @Route("/api/wallet/updateBalance/{id}", methods={"POST"}, name="api_wallet_update_balance")
     * @param Request $request
     * @param int $id
     * @param WalletService $walletService
     * @param TransactionService $transactionService
     * @return Response
     * @throws DbSaveException
     * @throws ExchangeRateException
     * @throws WalletNotFoundException
     * @throws CurrencyNotFoundException
     * @throws ConnectionException
     */
    public function updateBalance(
        Request $request,
        int $id,
        WalletService $walletService,
        TransactionService $transactionService
    ): Response
    {
        $walletService->setWallet($id);

        $transactionService
            ->setWallet($id)
            ->setCurrency($request->request->get('currency'))
            ->setAmount($request->get('amount'))
            ->add(
                $request->get('type'),
                $request->get('reason')
            );

        $walletService
            ->setCurrency($request->request->get('currency'))
            ->setAmount($request->get('amount'))
            ->updateBalance();

        return $this->json([
            'result' => true
        ]);
    }
}
