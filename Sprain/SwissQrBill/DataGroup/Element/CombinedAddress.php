<?php declare(strict_types=1);

namespace Pits\PitsQrCode\Sprain\SwissQrBill\DataGroup\Element;

use Pits\PitsQrCode\Sprain\SwissQrBill\DataGroup\AddressInterface;
use Pits\PitsQrCode\Sprain\SwissQrBill\DataGroup\QrCodeableInterface;
use Pits\PitsQrCode\Sprain\SwissQrBill\Validator\SelfValidatableInterface;
use Pits\PitsQrCode\Sprain\SwissQrBill\Validator\SelfValidatableTrait;
use Pits\PitsQrCode\Symfony\Component\Validator\Constraints as Assert;
use Pits\PitsQrCode\Symfony\Component\Validator\Mapping\ClassMetadata;

class CombinedAddress implements AddressInterface, SelfValidatableInterface, QrCodeableInterface
{
    use SelfValidatableTrait;

    const ADDRESS_TYPE = 'K';

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
     * Address line 1
     *
     * Street and building number or P.O. Box
     *
     * @var string
     */
    private $addressLine1;

    /**
     * Address line 2
     *
     * Postal code and town
     *
     * @var string
     */
    private $addressLine2;

    /**
     * Postcode
     *
     * Postal code
     *
     * @var string
     */
    private $postalcode;

    /**
     * City
     *
     * city
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

    public static function create(
        string $name,
        ?string $addressLine1,
        string $addressLine2,
        string $country,
        string $postalcode = NULL,
        string $city = NULL,
        string $street = NULL,
        ?string $buildingNumber= NULL
    ): self {
        $combinedAddress = new self();
        $combinedAddress->name = $name;
        $combinedAddress->addressLine1 = $addressLine1;
        $combinedAddress->addressLine2 = $addressLine2;
        $combinedAddress->country = strtoupper($country);

        $text = explode("<!Delimiter!>",$addressLine2 );
        $combinedAddress->postalcode = $text[0];
        $combinedAddress->city = $text[1];

        $combinedAddress->street =$addressLine1;
        $combinedAddress->buildingNumber = $text[2];


        return $combinedAddress;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalcode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getBuildingNumber(): ?string
    {
        return $this->buildingNumber;
    }

    public function getFullAddress(): string
    {
        $address = $this->getName();

        if ($this->getAddressLine1()) {
            $address .= "\n" . $this->getAddressLine1();
        }

        if (in_array($this->getCountry(), ['CH', 'FL'])) {
            $address .= "\n" . $this->getAddressLine2();
        } else {
            $address .= sprintf("\n%s-%s", $this->getCountry(), $this->getAddressLine2());
        }

        return $address;
    }

    public function getQrCodeData(): array
    {
        return [
            $this->getAddressLine2() ? self::ADDRESS_TYPE : '',
            $this->getName(),
            $this->getAddressLine1(),
            $this->getAddressLine2(),
            '',
            '',
            $this->getCountry()
        ];
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraints('name', [
            new Assert\NotBlank(),
            new Assert\Length([
                'max' => 70
            ])
        ]);

        $metadata->addPropertyConstraints('addressLine1', [
            new Assert\Length([
                'max' => 70
            ])
        ]);

        $metadata->addPropertyConstraints('addressLine2', [
            new Assert\NotBlank(),
            new Assert\Length([
                'max' => 70
            ])
        ]);

        $metadata->addPropertyConstraints('country', [
            new Assert\NotBlank(),
            new Assert\Country()
        ]);
    }
}
