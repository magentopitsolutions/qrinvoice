<?php declare(strict_types=1);

namespace Pits\PitsQrCode\Sprain\SwissQrBill\PaymentPart\Output;

use Pits\PitsQrCode\Sprain\SwissQrBill\QrBill;

interface OutputInterface
{
    public function getQrBill(): ?QrBill;

    public function getLanguage(): ?string;

    public function getPaymentPart();

    public function setPrintable(bool $printable);

    public function isPrintable(): bool;

    public function setQrCodeImageFormat(string $imageFormat);

    public function getQrCodeImageFormat(): string;
}
