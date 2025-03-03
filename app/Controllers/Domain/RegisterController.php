<?php

namespace Controllers\Domain;

use Models\Domain\Services\RegisterService;
use Zephyrus\Application\Form;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Post;
use Zephyrus\Application\Controller as BaseController;

class RegisterController extends BaseController
{
    private RegisterService $authService;

    public function __construct()
    {
        $this->authService = new RegisterService();
    }

    #[Post("/register")]
    public function register(): Response
    {
        try {
            $data = $this->request->getBody()->getParameters();

            if (empty($data)) {
                return $this->abortBadRequest("Aucune donnée envoyée ou format incorrect.");
            }

            $form = new Form($data);
            $result = $this->authService->registerUser($form);

            if (isset($result["error"])) {
                return $this->abortBadRequest($result["error"]);
            }

            return $this->json($result);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $this->abortBadRequest($e->getMessage());
        }
    }

}
