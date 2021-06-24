<?php declare(strict_types=1);

namespace Pits\PitsQrCode\Sprain\SwissQrBill\DataGroup\Element;

use Pits\PitsQrCode\Sprain\SwissQrBill\DataGroup\QrCodeableInterface;
use Pits\PitsQrCode\Sprain\SwissQrBill\Validator\SelfValidatableInterface;
use Pits\PitsQrCode\Sprain\SwissQrBill\Validator\SelfValidatableTrait;
use Pits\PitsQrCode\Symfony\Component\Validator\Constraints as Assert;
use Pits\PitsQrCode\Symfony\Component\Validator\Mapping\ClassMetadata;

class AlternativeScheme implements QrCodeableInterface, SelfValidatableInterface
{
    use SelfValidatableTrait;

    /**
     * Parameter character chain of the alternative scheme
     *
     * @var string
     */
    private $parameter;

    public static function create(string $parameter): self
    {
        $alternativeScheme = new self();
        $alternativeScheme->parameter = $parameter;

        return $alternativeScheme;
    }

    public function getParameter(): ?string
    {
        return $this->parameter;
    }

    public function getQrCodeData(): array
    {
        return [
            $this->getParameter()
        ];
    }

    /**
     * Note that no real-life alternative schemes yet exist. Therefore validation is kept simple yet.
     * @link https://www.paymentstandards.ch/en/home/software-partner/alternative-schemes.html
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraints('parameter', [
            new Assert\NotBlank(),
            new Assert\Length([
                'max' => 100
            ])
        ]);
    }
}
