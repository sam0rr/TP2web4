<?php

namespace Controllers\Domain;

use Controllers\Controller;
use Models\Domain\Services\TransactionService;
use Zephyrus\Network\ContentType;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Post;
use Zephyrus\Network\Router\Get;

class TransactionController extends Controller
{
    private TransactionService $transactionService;

    public function __construct()
    {
        $this->transactionService = new TransactionService();
    }

    #[Post("/{token}/transactions")]
    public function addTransaction(string $token): Response
    {
        try {
            $form = $this->buildForm();
            $result = $this->transactionService->addTransaction($token, $form);

            if (isset($result["errors"])) {
                return $this->abortBadRequest(json_encode(["errors" => $result["errors"]]), ContentType::JSON);
            }

            return $this->json($result);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $this->abortBadRequest(json_encode(["error" => "Une erreur s'est produite lors de la création de la transaction."]), ContentType::JSON);
        }
    }

    #[Get("/{token}/history")]
    public function getTransactionHistory(string $token): Response
    {
        try {
            $result = $this->transactionService->getUserTransactions($token);

            if (isset($result["errors"])) {
                return $this->abortBadRequest(json_encode(["errors" => $result["errors"]]), ContentType::JSON);
            }

            return $this->json($result);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $this->abortBadRequest(json_encode(["error" => "Une erreur s'est produite lors de la récupération de l'historique des transactions."]), ContentType::JSON);
        }
    }

}
