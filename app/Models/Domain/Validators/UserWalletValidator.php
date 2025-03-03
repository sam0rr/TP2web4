<?php

namespace Models\Domain\Validators;

use Models\Exceptions\FormException;
use Zephyrus\Application\Form;
use Zephyrus\Application\Rule;


class UserWalletValidator
{
    public static function assertCreditAmount(string $userType, Form $form): void
    {
        $form->field("credit", [
            Rule::required("Le montant du crédit est obligatoire."),
            Rule::decimal("Le montant du crédit doit être numérique."),
            Rule::greaterThan(0, "Le montant doit être supérieur à 0.")
        ]);

        if (!$form->verify()) {
            throw new FormException($form);
        }

        $credit = (float) $form->getValue("credit");
        $maxCredit = ($userType === "PREMIUM") ? 2000.00 : 500.00;

        if ($credit > $maxCredit) {
            $form->addError("credit", "Vous ne pouvez pas ajouter plus de $maxCredit par requête.");
        }

        if ($form->hasError()) {
            throw new FormException($form);
        }
    }

    public static function assertWithdrawAmount(Form $form, float $amount, float $balance): void
    {
        $form->field("credit", [
            Rule::required("Le montant du retrait est obligatoire."),
            Rule::decimal("Le montant doit être un nombre valide."),
            Rule::greaterThan(0, "Le montant doit être supérieur à 0.")
        ]);

        if (!$form->verify()) {
            throw new FormException($form);
        }

        if ($amount > $balance) {
            $form->addError("credit", "Fonds insuffisants.");
        }

        if ($form->hasError()) {
            throw new FormException($form);
        }
    }
}