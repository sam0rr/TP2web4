<?php

namespace Models\Domain\Validators;

use Models\Exceptions\FormException;
use Zephyrus\Application\Form;
use Zephyrus\Application\Rule;

class LoginValidator
{
    public static function assertLogin(Form $form): void
    {
        $form->field("username", [
            Rule::required("Le nom d'utilisateur est obligatoire.")
        ]);
        $form->field("password", [
            Rule::required("Le mot de passe est obligatoire.")
        ]);

        if (!$form->verify()) {
            throw new FormException($form);
        }
    }
}