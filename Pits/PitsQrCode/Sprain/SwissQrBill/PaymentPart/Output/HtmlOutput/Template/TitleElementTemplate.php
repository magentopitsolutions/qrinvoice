<?php declare(strict_types=1);

namespace Pits\PitsQrCode\Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template;

class TitleElementTemplate
{
    public const TEMPLATE = <<<EOT
<h2>{{ {{ title }} }}</h2>
EOT;
}
