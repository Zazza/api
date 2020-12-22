<?php

namespace App\Controller;

use App\Entity\StaticCurrency;
use App\Entity\Transaction;
use App\Repository\WalletRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiWalletController extends AbstractController
{
    /**
     * @Route("/api/wallet", name="api_wallet")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiWalletController.php',
        ]);
    }

    /**
     * @Route("/api/wallet/getBalance/{id}", methods={"GET"}, name="api_wallet_get_balance")
     * @param int $id
     * @param WalletRepository $walletRepository
     * @return Response
     */
    public function getBalance(int $id, WalletRepository $walletRepository): Response
    {
        $wallet = $walletRepository->find($id);

        if (!$wallet) {
            throw $this->createNotFoundException(
                'No wallet found for id '.$id
            );
        }

        return $this->json([
            'amount' => $wallet->getAmount()
        ]);
    }

    /**
     * @Route("/api/wallet/updateBalance/{id}", methods={"POST"}, name="api_wallet_update_balance")
     * @param Request $request
     * @param int $id
     * @param WalletRepository $walletRepository
     * @return Response
     */
    public function updateBalance(
        Request $request,
        int $id,
        WalletRepository $walletRepository,
        ValidatorInterface $validator
    ): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $wallet = $walletRepository->find($id);

        if (!$wallet) {
            throw $this->createNotFoundException(
                'No wallet found for id '.$id
            );
        }

        $currencyId = $request->request->get('currency');

        $currencyRepository = $this->getDoctrine()->getRepository(StaticCurrency::class);
        /** @var StaticCurrency $currency */
        $currency = $currencyRepository->find($currencyId);
        if (!$currency) {
            throw $this->createNotFoundException(
                'No currency found for id '.$currencyId
            );
        }

        $typeId = $request->get('type');
        $reasonId = $request->get('reason');
        $amount = $request->get('amount');

        $transaction = new Transaction();
        $transaction->setWallet($wallet);
        $transaction->setCurrency($currency);
        $transaction->setTypeId($typeId);
        $transaction->setReasonId($reasonId);
        $transaction->setAmount($amount);

        $errors = $validator->validate($transaction);
        if (count($errors) > 0) {
            return $this->json([
                'errors' => (string) $errors
            ], 400);
        }

        $entityManager->persist($transaction);
        $entityManager->flush();


        return $this->json([
            'message' => 'gtfgdg'
        ]);
    }
}
