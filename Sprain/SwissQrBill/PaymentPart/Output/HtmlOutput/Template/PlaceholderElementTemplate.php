<?php declare(strict_types=1);

namespace Pits\PitsQrCode\Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template;

class PlaceholderElementTemplate
{
    public const TEMPLATE = <<<EOT
<img src="{{ file }}" style="width:{{ width }}mm; height:{{ height }}mm;" class="qr-bill-placeholder" id="{{ id }}">
EOT;
}
