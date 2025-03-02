<?php

namespace Controllers\Domain;

use Controllers\Controller;
use Models\Domain\Services\AuthService;
use Zephyrus\Application\Form;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Post;

class AuthController extends Controller
{
    //#[Post("/login")]     // Connexion avec retour du token (refresh du token)

    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
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
