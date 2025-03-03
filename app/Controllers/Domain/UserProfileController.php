<?php

namespace Controllers\Domain;

use Controllers\Controller;
use Models\Domain\Services\UserProfileService;
use Zephyrus\Application\Form;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Post;
use Zephyrus\Network\Router\Get;
use Zephyrus\Network\Router\Put;

class UserProfileController extends Controller
{
    private UserProfileService $userProfileService;

    public function __construct()
    {
        $this-> userProfileService = new UserProfileService();
    }

    #[Post("/login")]
    public function login(): Response
    {
        $data = $this->request->getBody()->getParameters();

        if (empty($data)) {
            return $this->abortBadRequest("Aucune donnée envoyée.");
        }

        $form = new Form($data);
        $result = $this->userProfileService->authenticateUser($form);

        if (isset($result["errors"])) {
            return $this->json($result);
        }

        return $this->json($result);
    }

    #[Get("/profile/{token}")]
    public function getProfile(string $token): Response
    {
        $result = $this->userProfileService->getUserProfile($token);

        if (isset($result["errors"])) {
            return $this->json($result);
        }

        return $this->json($result);
    }

    #[Put("/profile/{token}")]
    public function updateProfile(string $token): Response
    {
        $data = $this->request->getBody()->getParameters();

        if (empty($data)) {
            return $this->abortBadRequest("Aucune donnée envoyée.");
        }

        $form = new Form($data);
        $result = $this->userProfileService->updateUserProfile($token, $form);

        if (isset($result["errors"])) {
            return $this->json($result);
        }

        return $this->json($result);
    }

    #[Put("/profile/{token}/password")]
    public function updatePassword(string $token): Response
    {
        $data = $this->request->getBody()->getParameters();

        if (empty($data)) {
            return $this->abortBadRequest("Aucune donnée envoyée.");
        }

        $form = new Form($data);
        $result = $this->userProfileService->updatePassword($token, $form);

        if (isset($result["errors"])) {
            return $this->json($result);
        }

        return $this->json($result);
    }

    #[Put("/profile/{token}/elevate")]
    public function elevateAccount(string $token): Response
    {
        $result = $this->userProfileService->elevateAccount($token);

        if (isset($result["errors"])) {
            return $this->json($result);
        }

        return $this->json($result);
    }

}
