<?php
declare(strict_types = 1);

namespace Pits\PitsQrCode\BaconQrCode\Renderer;

use Pits\PitsQrCode\BaconQrCode\Encoder\QrCode;

interface RendererInterface
{
    public function render(QrCode $qrCode) : string;
}
