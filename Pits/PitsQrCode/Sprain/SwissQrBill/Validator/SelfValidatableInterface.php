<?php declare(strict_types=1);

namespace Pits\PitsQrCode\Sprain\SwissQrBill\Validator;

use Pits\PitsQrCode\Symfony\Component\Validator\ConstraintViolationListInterface;
use Pits\PitsQrCode\Symfony\Component\Validator\Mapping\ClassMetadata;

interface SelfValidatableInterface
{
    public function getViolations(): ConstraintViolationListInterface;

    public function isValid(): bool;

    public static function loadValidatorMetadata(ClassMetadata $metadata): void;
}
