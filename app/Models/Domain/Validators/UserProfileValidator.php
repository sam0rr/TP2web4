<?php

namespace Models\Domain\Validators;

use Models\Exceptions\FormException;
use Models\Domain\Entities\UserProfile;
use Models\Domain\Entities\UserWallet;
use Zephyrus\Application\Form;
use Zephyrus\Application\Rule;

class UserProfileValidator
{
    public static function assertElevationEligibility(?UserProfile $user, ?UserWallet $wallet, Form $form, ): void
    {
        if (!$user) {
            $form->addError("user", "Utilisateur non trouvé.");
        }

        if ($user && $user->type === "PREMIUM") {
            $form->addError("user", "L'utilisateur est déjà PREMIUM.");
        }

        if (!$wallet || $wallet->totalSpent < 1000) {
            $form->addError("wallet", "L'utilisateur doit avoir dépensé au moins 1 000 $ pour être éligible à l'élévation.");
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
