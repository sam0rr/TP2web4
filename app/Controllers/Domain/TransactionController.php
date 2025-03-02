<?php

namespace Controllers\Domain;

use Controllers\Controller;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router\Post;
use Zephyrus\Network\Router\Get;

class TransactionController extends Controller
{

    //#[Post("/profile/{token}/transactions")] // ajout d'une transaction
    //#[Get("/profile/{token}/history")] // Voir l'historique des transactions

    public function before(): ?Response
    {
        // verifier si le token est bon.
        return null;
    }




}
