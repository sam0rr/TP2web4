<?php

namespace Controllers\Domain;

    use Controllers\Controller;
    use Models\Domain\Services\UserWalletService;
    use Zephyrus\Application\Form;
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
        $result = $this->userWalletService->getUserCredits($token);

        if (isset($result["errors"])) {
            return $this->json($result);
        }

        return $this->json($result);
    }

    #[Post("/profile/{token}/credits")]
    public function addCredits(string $token): Response
    {
        $data = $this->request->getBody()->getParameters();

        if (empty($data)) {
            return $this->abortBadRequest("Aucune donnée envoyée ou format incorrect.");
        }

        $creditAmount = floatval($data['credit']);
        $form = new Form($data);
        $result = $this->userWalletService->addCredits($token, $creditAmount , $form);

        if (isset($result["errors"])) {
            return $this->json($result);
        }

        return $this->json($result);
    }

    #[Post("/profile/{token}/withdraw")]
    public function withdrawCredits(string $token): Response
    {
        $data = $this->request->getBody()->getParameters();

        if (empty($data)) {
            return $this->abortBadRequest("Aucune donnée envoyée ou format incorrect.");
        }

        $withdrawAmount = floatval($data['credit']);
        $form = new Form($data);
        $result = $this->userWalletService->withdrawCredits($token, $withdrawAmount, $form);

        if (isset($result["errors"])) {
            return $this->json($result);
        }

        return $this->json($result);
    }


}

