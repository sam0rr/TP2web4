<?php

namespace Controllers\Domain;

use Controllers\Controller;
use Models\Domain\Services\UserProfileService;
use Zephyrus\Application\Form;
use Zephyrus\Exceptions\JsonParseException;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Post;
use Zephyrus\Network\Router\Get;


//john_doe92: KingReallyStrong123!
//jane_smith88: SecurePassword2022!
//alice_wonder77: WonderfulAlice23!
//bob_builder65: Builder123@789Man!
//charlie_brown54: CharlieSecure2022!
//emma_watson43: EmmaHashPassword!
//michael_scott32: MichaelStrong123!
//sarah_connor21: Terminator2023@!
//david_lee10: DavidEncrypt2023!
//linda_kim99: LindaSecure2024!

class AuthController extends Controller
{
    //#[Post("/login")]     // Connexion avec retour du token (refresh du token)

    private UserProfileService $userService;

    public function __construct()
    {
        $this->userService = new UserProfileService();
    }

    public function before(): ?Response
    {
        // verifier si le token est bon.
        return null;
    }

    #[Post("/register")]
    public function register(): Response
    {

        $data = $this->request->getBody()->getParameters();

        if (empty($data)) {
            return $this->abortBadRequest("Aucune donnée envoyée ou format incorrect.");
        }

        $form = new Form($data);
        $result = $this->userService->registerUser($form);

        if (isset($result["error"])) {
            return $this->abortBadRequest($result["error"]);
        }

        return $this->json($result);
    }

    #[Get("/patate")]
    public function patate(): Response
    {
        return $this->json(["caca" => "lolipop"]);
    }

}
