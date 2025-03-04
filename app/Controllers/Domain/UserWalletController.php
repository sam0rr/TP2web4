<?php

namespace Controllers\Domain;

use Controllers\Controller;
use Models\Domain\Services\UserWalletService;
use Zephyrus\Network\ContentType;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Post;
use Zephyrus\Network\Router\Get;

class UserWalletController extends Controller
{
    private UserWalletService $userWalletService;

    public function __construct()
    {
        $this->userWalletService = new UserWalletService();
    }

    #[Get("/profile/{token}/credits")]
    public function getCredits(string $token): Response
    {
        try {
            $result = $this->userWalletService->getUserCredits($token);

            if (isset($result["errors"])) {
                return $this->abortBadRequest(json_encode(["errors" => $result["errors"]]), ContentType::JSON);
            }

            return $this->json($result);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $this->abortBadRequest(json_encode(["error" => "Une erreur s'est produite lors de la récupération du solde."]), ContentType::JSON);
        }
    }

    #[Post("/profile/{token}/credits")]
    public function addCredits(string $token): Response
    {
        try {
            $form = $this->buildForm();
            $creditAmount = floatval($form->getValue("credit"));
            $result = $this->userWalletService->addCredits($token, $creditAmount, $form);

            if (isset($result["errors"])) {
                return $this->abortBadRequest(json_encode(["errors" => $result["errors"]]), ContentType::JSON);
            }

            return $this->json($result);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $this->abortBadRequest(json_encode(["error" => "Une erreur s'est produite lors de l'ajout de crédit."]), ContentType::JSON);
        }
    }

    #[Post("/profile/{token}/withdraw")]
    public function withdrawCredits(string $token): Response
    {
        try {
            $form = $this->buildForm();
            $withdrawAmount = floatval($form->getValue("credit"));
            $result = $this->userWalletService->withdrawCredits($token, $withdrawAmount, $form);

            if (isset($result["errors"])) {
                return $this->abortBadRequest(json_encode(["errors" => $result["errors"]]), ContentType::JSON);
            }

            return $this->json($result);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $this->abortBadRequest(json_encode(["error" => "Une erreur s'est produite lors du retrait de crédits."]), ContentType::JSON);
        }
    }

}
