<?php

namespace App\Controller;

use App\Exception\DbSaveException;
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
     * @param TransactionService $transaction
     * @return Response
     */
    public function getBalance(int $id, TransactionService $transaction): Response
    {
        if (!$transaction->setWallet($id)) {
            throw $this->createNotFoundException(
                'No wallet found for id '.$id
            );
        }

        return $this->json([
            'currency' => $transaction->getWallet()->getCurrency()->getName(),
            'amount' => $transaction->getWallet()->getAmount() / Convert::CAST
        ]);
    }

    /**
     * @Route("/api/wallet/updateBalance/{id}", methods={"POST"}, name="api_wallet_update_balance")
     * @param Request $request
     * @param int $id
     * @param TransactionService $transaction
     */
    public function updateBalance(
        Request $request,
        int $id,
        TransactionService $transaction
    ): Response
    {
        if (!$transaction->setWallet($id)) {
            throw $this->createNotFoundException(
                'No wallet found for id '.$id
            );
        }

        try {
            $transaction
                ->setCurrency($request->request->get('currency'))
                ->setAmount((float) $request->get('amount'))
                ->add(
                    $request->get('type'),
                    $request->get('reason')
                )
                ->walletAmount();
        } catch (DbSaveException $e) {
            return $this->json([
                'errors' => (string) $e->getMessage()
            ], 400);
        }

        return $this->json([
            'result' => true
        ]);
    }
}
