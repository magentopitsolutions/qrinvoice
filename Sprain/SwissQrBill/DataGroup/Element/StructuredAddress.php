<?php declare(strict_types=1);

namespace Pits\PitsQrCode\Sprain\SwissQrBill\DataGroup\Element;

use Pits\PitsQrCode\Sprain\SwissQrBill\DataGroup\AddressInterface;
use Pits\PitsQrCode\Sprain\SwissQrBill\DataGroup\QrCodeableInterface;
use Pits\PitsQrCode\Sprain\SwissQrBill\Validator\SelfValidatableInterface;
use Pits\PitsQrCode\Sprain\SwissQrBill\Validator\SelfValidatableTrait;
use Pits\PitsQrCode\Symfony\Component\Validator\Constraints as Assert;
use Pits\PitsQrCode\Symfony\Component\Validator\Mapping\ClassMetadata;

class StructuredAddress implements AddressInterface, SelfValidatableInterface, QrCodeableInterface
{
    use SelfValidatableTrait;

    const ADDRESS_TYPE = 'S';

    /**
     * company
     *
     * @var string
     */
    private $company;

    /**
     * Name or company
     *
     * @var string
     */
    private $name;

    /**
     * Street / P.O. box
     *
     * May not include building or house number.
     *
     * @var string
     */
    private $street;

    /**
     * Building number
     *
     * @var string
     */
    private $buildingNumber;

    /**
     * Postal code without county code
     *
     * @var string
     */
    private $postalCode;

    /**
     * City
     *
     * @var string
     */
    private $city;

    /**
     * Country (ISO 3166-1 alpha-2)
     *
     * @var string
     */
    private $country;

    public static function createWithoutStreet(
        string $name,
        string $postalCode,
        string $city,
        string $country
    ): self {
        $structuredAddress = new self();
        $structuredAddress->name = $name;
        $structuredAddress->postalCode = $postalCode;
        $structuredAddress->city = $city;
        $structuredAddress->country = strtoupper($country);

        return $structuredAddress;
    }

    public static function createWithStreet(
        ?string $company,
        ?string $name,
        string $street,
        ?string $buildingNumber,
        string $postalCode,
        string $city,
        string $country
    ): self {
        $structuredAddress = new self();
        $structuredAddress->company = $company;
        $structuredAddress->name = $name;
        $structuredAddress->street = $street;
        $structuredAddress->buildingNumber = $buildingNumber;
        $structuredAddress->postalCode = $postalCode;
        $structuredAddress->city = $city;
        $structuredAddress->country = strtoupper($country);

        return $structuredAddress;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getBuildingNumber(): ?string
    {
        return $this->buildingNumber;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getFullAddress(): string
    {
        $address = '';
        if ($this->getCompany()) {
            $address = $this->getCompany() . "\n";
        }
        if ($this->getName()) {
            $address .= $this->getName() . "\n";
        }

        if ($this->getStreet()) {
            $address .= $this->getStreet();

            if ($this->getBuildingNumber()) {
                $address .= " " . $this->getBuildingNumber();
            }
        }

        if (in_array($this->getCountry(), ['CH', 'FL'])) {
            $address .= sprintf("\n%s %s", $this->getPostalCode(), $this->getCity());
        } else {
            $address .= sprintf("\n%s-%s %s", $this->getCountry(), $this->getPostalCode(), $this->getCity());
        }

        return $address;
    }

    public function getQrCodeData(): array
    {
        if($this->getCompany())
            $payer = $this->getCompany();
        else
            $payer = $this->getName();

        return [
            $this->getCity() ? self::ADDRESS_TYPE : '',
            $payer,
            $this->getStreet(),
            $this->getBuildingNumber(),
            $this->getPostalCode(),
            $this->getCity(),
            $this->getCountry()
        ];
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraints('company', [
            new Assert\Length([
                'max' => 70
            ])
        ]);

        $metadata->addPropertyConstraints('name', [
            new Assert\Length([
                'max' => 70
            ])
        ]);

        $metadata->addPropertyConstraints('street', [
            new Assert\Length([
                'max' => 70
            ])
        ]);

        $metadata->addPropertyConstraints('buildingNumber', [
            new Assert\Length([
                'max' => 16
            ])
        ]);

        $metadata->addPropertyConstraints('postalCode', [
            new Assert\NotBlank(),
            new Assert\Length([
                'max' => 16
            ])
        ]);

        $metadata->addPropertyConstraints('city', [
            new Assert\NotBlank(),
            new Assert\Length([
                'max' => 35
            ])
        ]);

        $metadata->addPropertyConstraints('country', [
            new Assert\NotBlank(),
            new Assert\Country()
        ]);
    }
}
