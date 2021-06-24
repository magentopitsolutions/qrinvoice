<?php declare(strict_types=1);

namespace Pits\PitsQrCode\Sprain\SwissQrBill\Constraint;

use Pits\PitsQrCode\Symfony\Component\Validator\Constraint;

class ValidCreditorReference extends Constraint
{
    public $message = 'The string "{{ string }}" is not a valid Creditor Reference.';
}
