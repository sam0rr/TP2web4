<?php

namespace Models\Domain\Validators;

use Models\Exceptions\FormException;
use Zephyrus\Application\Form;
use Zephyrus\Application\Rule;

class TransactionValidator
{
    public static function assertTransaction(Form $form, string $userType): void
    {
        $form->field("item_name", [
            Rule::required("Le nom de l'article est obligatoire."),
            Rule::maxLength(255, "Le nom de l'article ne peut pas dépasser 255 caractères.")
        ]);

        $form->field("price", [
            Rule::required("Le prix est obligatoire."),
            Rule::decimal("Le prix doit être un nombre valide."),
            Rule::greaterThan(0, "Le prix doit être supérieur à zéro.")
        ]);

        $form->field("quantity", [
            Rule::required("La quantité est obligatoire."),
            Rule::integer("La quantité doit être un entier."),
            Rule::greaterThan(0, "La quantité doit être supérieure à zéro.")
        ]);

        if ($userType === "NORMAL" && $form->getValue("price") > 30) {
            $form->addError("price", "Les membres NORMAL ne peuvent pas acheter un article à plus de 30$.");
        }

        if (!$form->verify()) {
            throw new FormException($form);
        }
    }
}
