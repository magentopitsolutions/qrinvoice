<?php declare(strict_types=1);

namespace Pits\PitsQrCode\Sprain\SwissQrBill\DataGroup;

interface QrCodeableInterface
{
    public function getQrCodeData(): array;
}
