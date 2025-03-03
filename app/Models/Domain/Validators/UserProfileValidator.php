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

    public static function assertChangePassword(Form $form): void
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


}
