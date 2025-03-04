<?php

namespace Controllers\Domain;

use Models\Domain\Services\RegisterService;
use Zephyrus\Network\ContentType;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Post;
use Zephyrus\Application\Controller as BaseController;

class RegisterController extends BaseController
{
    private RegisterService $registerService;

    public function __construct()
    {
        $this->registerService = new RegisterService();
    }

    #[Post("/register")]
    public function register(): Response
    {
        try {
            $form = $this->buildForm();
            $result = $this->registerService->registerUser($form);

            if (isset($result["errors"])) {
                return $this->abortBadRequest(json_encode(["errors" => $result["errors"]]), ContentType::JSON);
            }

            return $this->json($result);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $this->abortBadRequest(json_encode(["error" => "Une erreur s'est produite lors de l'enregistrement de l'usager."]), ContentType::JSON);
        }
    }

}
