<?php declare(strict_types=1);

namespace Pits\PitsQrCode\Sprain\SwissQrBill\PaymentPart\Output;

use Pits\PitsQrCode\Sprain\SwissQrBill\DataGroup\Element\PaymentReference;
use Pits\PitsQrCode\Sprain\SwissQrBill\PaymentPart\Output\Element\Placeholder;
use Pits\PitsQrCode\Sprain\SwissQrBill\PaymentPart\Output\Element\Text;
use Pits\PitsQrCode\Sprain\SwissQrBill\PaymentPart\Output\Element\Title;
use Pits\PitsQrCode\Sprain\SwissQrBill\QrBill;
use Pits\PitsQrCode\Sprain\SwissQrBill\QrCode\QrCode;

abstract class AbstractOutput
{
    /** @var QrBill */
    protected $qrBill;

    /** @var  string */
    protected $language;

    /** @var bool */
    protected $printable;

    /** @var string */
    protected $qrCodeImageFormat;

    public function __construct(QrBill $qrBill, string $language)
    {
        $this->qrBill = $qrBill;
        $this->language = $language;
        $this->printable = false;
        $this->qrCodeImageFormat = QrCode::FILE_FORMAT_SVG;
    }

    public function getQrBill(): ?QrBill
    {
        return $this->qrBill;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setPrintable(bool $printable): self
    {
        $this->printable = $printable;

        return $this;
    }

    public function isPrintable(): bool
    {
        return $this->printable;
    }

    public function setQrCodeImageFormat(string $fileExtension): self
    {
        $this->qrCodeImageFormat = $fileExtension;

        return $this;
    }

    public function getQrCodeImageFormat(): string
    {
        return $this->qrCodeImageFormat;
    }

    protected function getInformationElements(): array
    {
        $informationElements = [];

        $informationElements[] = Title::create('text.creditor');
        $informationElements[] = Text::create($this->qrBill->getCreditorInformation()->getFormattedIban() . "\n" . $this->qrBill->getCreditor()->getFullAddress());

        if ($this->qrBill->getPaymentReference()->getType() !== PaymentReference::TYPE_NON) {
            $informationElements[] = Title::create('text.reference');
            $informationElements[] = Text::create($this->qrBill->getPaymentReference()->getFormattedReference());
        }

        if ($this->qrBill->getAdditionalInformation()) {
            $informationElements[] = Title::create('text.additionalInformation');
            $informationElements[] = Text::create($this->qrBill->getAdditionalInformation()->getFormattedString());
        }

        if ($this->qrBill->getUltimateDebtor()) {
            $informationElements[] = Title::create('text.payableBy');
            $informationElements[] = Text::create($this->qrBill->getUltimateDebtor()->getFullAddress());
        } else {
            $informationElements[] = Title::create('text.payableByName');
            $informationElements[] = Placeholder::create(Placeholder::PLACEHOLDER_TYPE_PAYABLE_BY);
        }

        return $informationElements;
    }

    protected function getInformationElementsOfReceipt(): array
    {
        $informationElements = [];

        $informationElements[] = Title::create('text.creditor');
        $informationElements[] = Text::create($this->qrBill->getCreditorInformation()->getFormattedIban() . "\n" . $this->qrBill->getCreditor()->getFullAddress());

        if ($this->qrBill->getPaymentReference()->getType() !== PaymentReference::TYPE_NON) {
            $informationElements[] = Title::create('text.reference');
            $informationElements[] = Text::create($this->qrBill->getPaymentReference()->getFormattedReference());
        }

        if ($this->qrBill->getUltimateDebtor()) {
            $informationElements[] = Title::create('text.payableBy');
            $informationElements[] = Text::create($this->qrBill->getUltimateDebtor()->getFullAddress());
        } else {
            $informationElements[] = Title::create('text.payableByName');
            $informationElements[] = Placeholder::create(Placeholder::PLACEHOLDER_TYPE_PAYABLE_BY_RECEIPT);
        }

        return $informationElements;
    }

    protected function getCurrencyElements(): array
    {
        $currencyElements = [];

        $currencyElements[] = Title::create('text.currency');
        $currencyElements[] = Text::create($this->qrBill->getPaymentAmountInformation()->getCurrency());

        return $currencyElements;
    }

    protected function getAmountElements(): array
    {
        $amountElements = [];

        $amountElements[] = Title::create('text.amount');

        if (null === $this->qrBill->getPaymentAmountInformation()->getAmount()) {
            $amountElements[] = Placeholder::create(Placeholder::PLACEHOLDER_TYPE_AMOUNT);
        } else {
            $amountElements[] = Text::create($this->qrBill->getPaymentAmountInformation()->getFormattedAmount());
        }

        return $amountElements;
    }

    protected function getAmountElementsReceipt(): array
    {
        $amountElements = [];

        $amountElements[] = Title::create('text.amount');

        if (null === $this->qrBill->getPaymentAmountInformation()->getAmount()) {
            $amountElements[] = Placeholder::create(Placeholder::PLACEHOLDER_TYPE_AMOUNT_RECEIPT);
        } else {
            $amountElements[] = Text::create($this->qrBill->getPaymentAmountInformation()->getFormattedAmount());
        }

        return $amountElements;
    }

    protected function getFurtherInformationElements(): array
    {
        $furtherInformationElements = [];

        $furtherInformationLines= [];
        foreach ($this->qrBill->getAlternativeSchemes() as $alternativeScheme) {
            $furtherInformationLines[] = $alternativeScheme->getParameter();
        }
        $furtherInformationElements[] = Text::create(implode("\n", $furtherInformationLines));

        return $furtherInformationElements;
    }

    protected function getQrCode(): QrCode
    {
        $qrCode = $this->qrBill->getQrCode();
        $qrCode->setWriterByExtension($this->getQrCodeImageFormat());

        return $qrCode;
    }
}
