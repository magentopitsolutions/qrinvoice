<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pits\PitsQrCode\Symfony\Component\Intl\Data\Bundle\Writer;

/**
 * Writes resource bundle files.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @internal
 */
interface BundleWriterInterface
{
    /**
     * Writes data to a resource bundle.
     *
     * @param mixed $data The data to write
     */
    public function write(string $path, string $locale, $data);
}
