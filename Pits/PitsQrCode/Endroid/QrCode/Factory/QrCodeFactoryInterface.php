<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Pits\PitsQrCode\Endroid\QrCode\Factory;

use Pits\PitsQrCode\Endroid\QrCode\QrCodeInterface;

interface QrCodeFactoryInterface
{
    public function create(string $text = '', array $options = []): QrCodeInterface;
}
