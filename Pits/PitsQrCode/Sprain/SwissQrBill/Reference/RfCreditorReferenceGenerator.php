<?php declare(strict_types=1);

namespace Pits\PitsQrCode\Sprain\SwissQrBill\Reference;

use Pits\PitsQrCode\kmukku\phpIso11649\phpIso11649;
use Pits\PitsQrCode\Sprain\SwissQrBill\String\StringModifier;
use Pits\PitsQrCode\Sprain\SwissQrBill\Validator\Exception\InvalidCreditorReferenceException;
use Pits\PitsQrCode\Sprain\SwissQrBill\Validator\SelfValidatableInterface;
use Pits\PitsQrCode\Sprain\SwissQrBill\Validator\SelfValidatableTrait;
use Pits\PitsQrCode\Symfony\Component\Validator\Constraints as Assert;
use Pits\PitsQrCode\Symfony\Component\Validator\Mapping\ClassMetadata;

class RfCreditorReferenceGenerator implements SelfValidatableInterface
{
    use SelfValidatableTrait;

    /** @var string */
    protected $reference;

    public static function generate(string $reference) : string
    {
        $generator = new self($reference);

        return $generator->doGenerate();
    }

    public function __construct(string $reference)
    {
        $this->reference = StringModifier::stripWhitespace($reference);
    }

    public function doGenerate() : string
    {
        if (!$this->isValid()) {
            throw new InvalidCreditorReferenceException(
                'The provided data is not valid to generate a creditor reference.'
            );
        }

        $generator = new phpIso11649();

        return $generator->generateRfReference($this->reference, false);
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraints('reference', [
            new Assert\Regex([
                'pattern' => '/^[a-zA-Z0-9]*$/',
                'match' => true
            ]),
            new Assert\Length([
                'min' => 1,
                'max' => 21 // 25 minus 'RF' prefix minus 2-digit check sum
            ]),
            new Assert\NotBlank()
        ]);
    }
}
