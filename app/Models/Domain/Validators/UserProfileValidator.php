<?php

namespace Models\Domain\Validators;

use Zephyrus\Application\Form;
use Zephyrus\Application\Rule;
use Models\Exceptions\FormException;

class UserProfileValidator
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

    public static function assertUpdate(Form $form): void
    {

        if (isset($form->getFields()["email"])) {
            $form->field("email", [
                Rule::required("L'email est obligatoire."),
                Rule::email("L'email n'est pas valide.")
            ]);
        }

        if (!$form->verify()) {
            throw new FormException($form);
        }
    }

    public static function assertPasswordUpdate(Form $form): void
    {
        $form->field("old_password", [
            Rule::required("L'ancien mot de passe est obligatoire.")
        ]);

        $form->field("new_password", [
            Rule::required("Le mot de passe est obligatoire."),
            Rule::minLength(8, "Le mot de passe doit contenir au moins 8 caractères."),
        ]);

        if (!$form->verify()) {
            throw new FormException($form);
        }

        $oldPassword = $form->getValue("old_password");
        $newPassword = $form->getValue("new_password");

        if ($oldPassword === $newPassword) {
            $form->addError("new_password", "Le nouveau mot de passe doit être différent de l'ancien.");
            throw new FormException($form);
        }
    }
}
