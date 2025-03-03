<?php

namespace Controllers\Domain;

use Models\Domain\Services\LoginService;
use Zephyrus\Application\Form;
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
        $data = $this->request->getBody()->getParameters();

        if (empty($data)) {
            return $this->abortBadRequest("Aucune donnée envoyée.");
        }

        $form = new Form($data);
        $result = $this->loginService->authenticateUser($form);

        if (isset($result["errors"])) {
            return $this->json($result);
        }

        return $this->json($result);
    }

}