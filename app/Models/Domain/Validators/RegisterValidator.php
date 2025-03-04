<?php

namespace Models\Domain\Validators;

use Models\Domain\Brokers\RegisterBroker;
use Models\Exceptions\FormException;
use Zephyrus\Application\Form;
use Zephyrus\Application\Rule;

class RegisterValidator
{
    public static function assertRegister(Form $form, RegisterBroker $broker): void
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

        if ($broker->usernameExists($form->getValue('username'))) {
            $form->addError("username", "Nom d'utilisateur déjà utilisé.");
        }
        if ($broker->emailExists($form->getValue('email'))) {
            $form->addError("email", "Email déjà utilisé.");
        }

        if (!$form->verify()) {
            throw new FormException($form);
        }
    }
}
