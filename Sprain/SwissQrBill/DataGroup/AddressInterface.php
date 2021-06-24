<?php declare(strict_types=1);

namespace Pits\PitsQrCode\Sprain\SwissQrBill\DataGroup;

interface AddressInterface
{
    public function getName(): ?string;

    public function getCountry(): ?string;

    public function getFullAddress(): string;
}
