<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Pits\PitsQrCode\Endroid\QrCode\Factory;

use Pits\PitsQrCode\Endroid\QrCode\ErrorCorrectionLevel;
use Pits\PitsQrCode\Endroid\QrCode\Exception\ValidationException;
use Pits\PitsQrCode\Endroid\QrCode\QrCode;
use Pits\PitsQrCode\Endroid\QrCode\QrCodeInterface;
use Pits\PitsQrCode\Endroid\QrCode\WriterRegistryInterface;
use Pits\PitsQrCode\Symfony\Component\OptionsResolver\OptionsResolver;
use Pits\PitsQrCode\Symfony\Component\PropertyAccess\PropertyAccess;

class QrCodeFactory implements QrCodeFactoryInterface
{
    private $writerRegistry;

    /** @var OptionsResolver */
    private $optionsResolver;

    private $defaultOptions;

    /** @var array */
    private $definedOptions = [
        'writer',
        'writer_options',
        'size',
        'margin',
        'foreground_color',
        'background_color',
        'encoding',
        'round_block_size',
        'round_block_size_mode',
        'error_correction_level',
        'logo_path',
        'logo_width',
        'logo_height',
        'label',
        'label_font_size',
        'label_font_path',
        'label_alignment',
        'label_margin',
        'validate_result',
    ];

    public function __construct(array $defaultOptions = [], WriterRegistryInterface $writerRegistry = null)
    {
        $this->defaultOptions = $defaultOptions;
        $this->writerRegistry = $writerRegistry;
    }

    public function create(string $text = '', array $options = []): QrCodeInterface
    {
        $options = $this->getOptionsResolver()->resolve($options);
        $accessor = PropertyAccess::createPropertyAccessor();

        $qrCode = new QrCode($text);

        if ($this->writerRegistry instanceof WriterRegistryInterface) {
            $qrCode->setWriterRegistry($this->writerRegistry);
        }

        foreach ($this->definedOptions as $option) {
            if (isset($options[$option])) {
                if ('writer' === $option) {
                    $options['writer_by_name'] = $options[$option];
                    $option = 'writer_by_name';
                }
                if ('error_correction_level' === $option) {
                    $options[$option] = new ErrorCorrectionLevel($options[$option]);
                }
                $accessor->setValue($qrCode, $option, $options[$option]);
            }
        }

        if (!$qrCode instanceof QrCodeInterface) {
            throw new ValidationException('QR Code was messed up by property accessor');
        }

        return $qrCode;
    }

    private function getOptionsResolver(): OptionsResolver
    {
        if (!$this->optionsResolver instanceof OptionsResolver) {
            $this->optionsResolver = $this->createOptionsResolver();
        }

        return $this->optionsResolver;
    }

    private function createOptionsResolver(): OptionsResolver
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver
            ->setDefaults($this->defaultOptions)
            ->setDefined($this->definedOptions)
        ;

        return $optionsResolver;
    }
}
