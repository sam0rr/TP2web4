<?php

namespace Controllers\Domain;

use Controllers\Controller;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Post;
use Zephyrus\Network\Router\Get;

class UserWalletController extends Controller
{
   // #[Get("/profile/{token}/credits")]       // Consulter le solde d'un user
   // #[Post("/profile/{token}/credits")]     // Ajouter des fonds au portefeuille
    ////#[Post("/profile/{token}/withdraw")]    // Retirer de l’argent du portefeuille

    public function before(): ?Response
    {
        // verifier si le token est bon.
        return null;
    }

}
