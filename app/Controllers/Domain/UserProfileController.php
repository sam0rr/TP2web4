<?php

namespace Controllers\Domain;

use Controllers\Controller;
use Models\Domain\Services\UserProfileService;
use Zephyrus\Network\ContentType;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Get;
use Zephyrus\Network\Router\Put;

class UserProfileController extends Controller
{
    private UserProfileService $userProfileService;

    public function __construct()
    {
        $this->userProfileService = new UserProfileService();
    }

    #[Get("/{token}")]
    public function getProfile(string $token): Response
    {
        try {
            $result = $this->userProfileService->getUserProfile($token);

            if (isset($result["errors"])) {
                return $this->abortBadRequest(json_encode(["errors" => $result["errors"]]), ContentType::JSON);
            }

            return $this->json($result);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $this->abortBadRequest(json_encode(["error" => "Une erreur s'est produite lors de la récupération du profil."]), ContentType::JSON);
        }
    }

    #[Put("/{token}")]
    public function updateProfile(string $token): Response
    {
        try {
            $form = $this->buildForm();
            $result = $this->userProfileService->updateUserProfile($token, $form);

            if (isset($result["errors"])) {
                return $this->abortBadRequest(json_encode(["errors" => $result["errors"]]), ContentType::JSON);
            }

            return $this->json($result);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $this->abortBadRequest(json_encode(["error" => "Une erreur s'est produite lors de l'update du profil de l'usager."]), ContentType::JSON);
        }
    }

    #[Put("/{token}/password")]
    public function updatePassword(string $token): Response
    {
        try {
            $form = $this->buildForm();
            $result = $this->userProfileService->updatePassword($token, $form);

            if (isset($result["errors"])) {
                return $this->abortBadRequest(json_encode(["errors" => $result["errors"]]), ContentType::JSON);
            }

            return $this->json($result);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $this->abortBadRequest(json_encode(["error" => "Une erreur s'est produite lors de l'update du mot de passe de l'usager."]), ContentType::JSON);
        }
    }

    #[Put("/{token}/elevate")]
    public function elevateAccount(string $token): Response
    {
        try {
            $form = $this->buildForm();
            $result = $this->userProfileService->elevateAccount($token, $form);

            if (isset($result["errors"])) {
                return $this->abortBadRequest(json_encode(["errors" => $result["errors"]]), ContentType::JSON);
            }

            return $this->json($result);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $this->abortBadRequest(json_encode(["error" => "Une erreur s'est produite lors de l'élévation du compte."]), ContentType::JSON);
        }
    }

}
