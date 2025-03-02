<?php

namespace Models\Domain\Validators;

use Models\Exceptions\FormException;
use Zephyrus\Application\Form;
use Zephyrus\Application\Rule;

class AuthValidator
{
    public static function assert(Form $form): void
    {
        $form->field("username", [
            Rule::required("Le nom d'utilisateur est obligatoire.")
        ]);
        $form->field("email", [
            Rule::required("L'email est obligatoire."),
            Rule::email("L'email n'est pas valide.")
        ]);
        $form->field("password", [
            Rule::required("Le mot de passe est obligatoire."),
            Rule::minLength(8, "Le mot de passe doit contenir au moins 8 caractères.")
        ]);
        $form->field("firstname", [
            Rule::required("Le prénom est obligatoire.")
        ]);
        $form->field("lastname", [
            Rule::required("Le nom de famille est obligatoire.")
        ]);

        if (!$form->verify()) {
            throw new FormException($form);
        }
    }

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
