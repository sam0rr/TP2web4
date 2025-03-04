<?php

namespace Controllers\Domain;

use Models\Domain\Services\LoginService;
use Zephyrus\Network\ContentType;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Post;
use Zephyrus\Application\Controller as BaseController;

class LoginController extends BaseController
{
    private LoginService $loginService;

    public function __construct()
    {
        $this-> loginService = new LoginService();
    }

    #[Post("/login")]
    public function login(): Response
    {
        try {
            $form = $this->buildForm();
            $result = $this->loginService->authenticateUser($form);

            if (isset($result["errors"])) {
                return $this->abortBadRequest(json_encode(["errors" => $result["errors"]]), ContentType::JSON);
            }

            return $this->json($result);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $this->abortBadRequest(json_encode(["error" => "Une erreur s'est produite lors de la connexion."]), ContentType::JSON);
        }
    }



}