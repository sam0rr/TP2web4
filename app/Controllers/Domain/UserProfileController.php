<?php

namespace Controllers\Domain;

use Controllers\Controller;
use Models\Domain\Services\UserProfileService;
use Zephyrus\Application\Form;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Post;
use Zephyrus\Network\Router\Put;
use Zephyrus\Network\Router\Get;

class UserProfileController extends Controller
{

    //#[Get("/profile/{token}")]       // Récupérer les infos d'un utilisateur
    //#[Put("/profile/{token}")]     // Mettre à jour les infos (email, username, etc.)
    //#[Put("/profile/{token}/password")]   // Changer le mot de pass
    //#[Post("/profile/elevate")]   // Normal -> premium

    public function before(): ?Response
    {
        // verifier si le token est bon.
        return null;
    }
    #[Get("/profile/{token}")]
    public function login(): Response
    {
        $data = $this->request->getBody()->getParameters();

        if (empty($data)) {
            return $this->abortBadRequest("Aucune donnée envoyée.");
        }

        $form = new Form($data);
        $result = $this->authService->authenticateUser($form);

        if (isset($result["errors"])) {
            return $this->json($result);
        }

        return $this->json($result);
    }

}
