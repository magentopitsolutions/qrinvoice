<?php declare(strict_types=1);

namespace Pits\PitsQrCode\Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template;

class TextElementTemplate
{
    public const TEMPLATE = <<<EOT
<p>{{ text }}</p>
EOT;
}
