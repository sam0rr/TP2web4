<?php

namespace Controllers\Domain;

use Controllers\Controller;
use Models\Domain\Services\TransactionService;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Post;
use Zephyrus\Network\Router\Get;
use Zephyrus\Application\Form;

class TransactionController extends Controller
{
    private TransactionService $transactionService;

    public function __construct()
    {
        $this->transactionService = new TransactionService();
    }

    #[Post("/profile/{token}/transactions")]
    public function addTransaction(string $token): Response
    {
        $data = $this->request->getBody()->getParameters();

        if (empty($data)) {
            return $this->abortBadRequest("Aucune donnée envoyée.");
        }

        $form = new Form($data);
        $result = $this->transactionService->addTransaction($token, $form);

        if (isset($result["errors"])) {
            return $this->json($result);
        }

        return $this->json($result);
    }

    #[Get("/profile/{token}/history")]
    public function getTransactionHistory(string $token): Response
    {
        $transactions = $this->transactionService->getUserTransactions($token);

        return $this->json($transactions);
    }
}
