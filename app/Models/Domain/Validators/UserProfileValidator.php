<?php

namespace Models\Domain\Validators;

use Zephyrus\Application\Form;
use Zephyrus\Application\Rule;
use Models\Exceptions\FormException;

class UserProfileValidator
{
    public static function assert(Form $form): void
    {
        $form->field("username", [
            Rule::required("Username is required.")
        ]);
        $form->field("email", [
            Rule::required("Email is required."),
            Rule::email("Invalid email address.")
        ]);
        $form->field("password", [
            Rule::required("Password is required."),
            Rule::minLength(8, "Password must be at least 8 characters.")
        ]);
        $form->field("firstname", [
            Rule::required("First name is required.")
        ]);
        $form->field("lastname", [
            Rule::required("Last name is required.")
        ]);

        if (!$form->verify()) {
            throw new FormException($form);
        }
    }
}
