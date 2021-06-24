<?php declare(strict_types=1);

namespace Pits\PitsQrCode\Sprain\SwissQrBill\Constraint;

use Pits\PitsQrCode\kmukku\phpIso11649\phpIso11649;
use Pits\PitsQrCode\Symfony\Component\Validator\Constraint;
use Pits\PitsQrCode\Symfony\Component\Validator\ConstraintValidator;
use Pits\PitsQrCode\Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidCreditorReferenceValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ValidCreditorReference) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\ValidCreditorReference');
        }

        if (null === $value || '' === $value) {
            return;
        }

        $referenceGenerator = new phpIso11649();

        if (false === $referenceGenerator->validateRfReference($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
