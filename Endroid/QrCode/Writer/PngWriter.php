<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Pits\PitsQrCode\Endroid\QrCode\Writer;

use Pits\PitsQrCode\Endroid\QrCode\Exception\GenerateImageException;
use Pits\PitsQrCode\Endroid\QrCode\Exception\MissingFunctionException;
use Pits\PitsQrCode\Endroid\QrCode\Exception\MissingLogoHeightException;
use Pits\PitsQrCode\Endroid\QrCode\Exception\ValidationException;
use Pits\PitsQrCode\Endroid\QrCode\LabelAlignment;
use Pits\PitsQrCode\Endroid\QrCode\QrCodeInterface;
use Zxing\QrReader;

class PngWriter extends AbstractWriter
{
    public function writeString(QrCodeInterface $qrCode): string
    {
        if (!extension_loaded('gd')) {
            throw new GenerateImageException('Unable to generate image: check your GD installation');
        }

        $image = $this->createImage($qrCode->getData(), $qrCode);

        $logoPath = $qrCode->getLogoPath();
        if (null !== $logoPath) {
            $image = $this->addLogo($image, $logoPath, $qrCode->getLogoWidth(), $qrCode->getLogoHeight());
        }

        $label = $qrCode->getLabel();
        if (null !== $label) {
            $image = $this->addLabel($image, $label, $qrCode->getLabelFontPath(), $qrCode->getLabelFontSize(), $qrCode->getLabelAlignment(), $qrCode->getLabelMargin(), $qrCode->getForegroundColor(), $qrCode->getBackgroundColor());
        }

        $string = $this->imageToString($image);

        imagedestroy($image);

        if ($qrCode->getValidateResult()) {
            $reader = new QrReader($string, QrReader::SOURCE_TYPE_BLOB);
            if ($reader->text() !== $qrCode->getText()) {
                throw new ValidationException('Built-in validation reader read "'.$reader->text().'" instead of "'.$qrCode->getText().'".
                     Adjust your parameters to increase readability or disable built-in validation.');
            }
        }

        return $string;
    }

    private function createImage(array $data, QrCodeInterface $qrCode)
    {
        $baseSize = $qrCode->getRoundBlockSize() ? $data['block_size'] : 25;

        $baseImage = $this->createBaseImage($baseSize, $data, $qrCode);
        $interpolatedImage = $this->createInterpolatedImage($baseImage, $data, $qrCode);

        imagedestroy($baseImage);

        return $interpolatedImage;
    }

    private function createBaseImage(int $baseSize, array $data, QrCodeInterface $qrCode)
    {
        $image = imagecreatetruecolor($data['block_count'] * $baseSize, $data['block_count'] * $baseSize);

        if ((!is_resource($image)) && (!($image instanceof \GdImage)) ) {
            throw new GenerateImageException('Unable to generate image: check your GD installation');
        }

        $foregroundColor = imagecolorallocatealpha($image, $qrCode->getForegroundColor()['r'], $qrCode->getForegroundColor()['g'], $qrCode->getForegroundColor()['b'], $qrCode->getForegroundColor()['a']);
        $backgroundColor = imagecolorallocatealpha($image, $qrCode->getBackgroundColor()['r'], $qrCode->getBackgroundColor()['g'], $qrCode->getBackgroundColor()['b'], $qrCode->getBackgroundColor()['a']);
        imagefill($image, 0, 0, $backgroundColor);

        foreach ($data['matrix'] as $row => $values) {
            foreach ($values as $column => $value) {
                if (1 === $value) {
                    imagefilledrectangle($image, $column * $baseSize, $row * $baseSize, intval(($column + 1) * $baseSize), intval(($row + 1) * $baseSize), $foregroundColor);
                }
            }
        }

        return $image;
    }

    private function createInterpolatedImage($baseImage, array $data, QrCodeInterface $qrCode)
    {
        $image = imagecreatetruecolor($data['outer_width'], $data['outer_height']);

        if ((!is_resource($image)) && (!($image instanceof \GdImage))) {
            throw new GenerateImageException('Unable to generate image: check your GD installation');
        }

        $backgroundColor = imagecolorallocatealpha($image, $qrCode->getBackgroundColor()['r'], $qrCode->getBackgroundColor()['g'], $qrCode->getBackgroundColor()['b'], $qrCode->getBackgroundColor()['a']);
        imagefill($image, 0, 0, $backgroundColor);
        imagecopyresampled($image, $baseImage, (int) $data['margin_left'], (int) $data['margin_left'], 0, 0, (int) $data['inner_width'], (int) $data['inner_height'], imagesx($baseImage), imagesy($baseImage));

        if ($qrCode->getBackgroundColor()['a'] > 0) {
            imagesavealpha($image, true);
        }

        return $image;
    }

    private function addLogo($sourceImage, string $logoPath, int $logoWidth = null, int $logoHeight = null)
    {
        $mimeType = $this->getMimeType($logoPath);
        $logoImage = imagecreatefromstring(strval(file_get_contents($logoPath)));

        if ('image/svg+xml' === $mimeType && (null === $logoHeight || null === $logoWidth)) {
            throw new MissingLogoHeightException('SVG Logos require an explicit height set via setLogoSize($width, $height)');
        }

        if ((!is_resource($logoImage)) && (!($logoImage instanceof \GdImage))) {
            throw new GenerateImageException('Unable to generate image: check your GD installation or logo path');
        }

        $logoSourceWidth = imagesx($logoImage);
        $logoSourceHeight = imagesy($logoImage);

        if (null === $logoWidth) {
            $logoWidth = $logoSourceWidth;
        }

        if (null === $logoHeight) {
            $aspectRatio = $logoWidth / $logoSourceWidth;
            $logoHeight = intval($logoSourceHeight * $aspectRatio);
        }

        $logoX = imagesx($sourceImage) / 2 - $logoWidth / 2;
        $logoY = imagesy($sourceImage) / 2 - $logoHeight / 2;

        imagecopyresampled($sourceImage, $logoImage, intval($logoX), intval($logoY), 0, 0, $logoWidth, $logoHeight, $logoSourceWidth, $logoSourceHeight);

        imagedestroy($logoImage);

        return $sourceImage;
    }

    private function addLabel($sourceImage, string $label, string $labelFontPath, int $labelFontSize, string $labelAlignment, array $labelMargin, array $foregroundColor, array $backgroundColor)
    {
        if (!function_exists('imagettfbbox')) {
            throw new MissingFunctionException('Missing function "imagettfbbox", please make sure you installed the FreeType library');
        }

        $labelBox = imagettfbbox($labelFontSize, 0, $labelFontPath, $label);
        $labelBoxWidth = intval($labelBox[2] - $labelBox[0]);
        $labelBoxHeight = intval($labelBox[0] - $labelBox[7]);

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        $targetWidth = $sourceWidth;
        $targetHeight = $sourceHeight + $labelBoxHeight + $labelMargin['t'] + $labelMargin['b'];

        // Create empty target image
        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        if ((!is_resource($targetImage)) && (!($targetImage instanceof \GdImage))) {
            throw new GenerateImageException('Unable to generate image: check your GD installation');
        }

        $foregroundColor = imagecolorallocate($targetImage, $foregroundColor['r'], $foregroundColor['g'], $foregroundColor['b']);
        $backgroundColor = imagecolorallocate($targetImage, $backgroundColor['r'], $backgroundColor['g'], $backgroundColor['b']);
        imagefill($targetImage, 0, 0, $backgroundColor);

        // Copy source image to target image
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $sourceWidth, $sourceHeight, $sourceWidth, $sourceHeight);

        imagedestroy($sourceImage);

        switch ($labelAlignment) {
            case LabelAlignment::LEFT:
                $labelX = $labelMargin['l'];
                break;
            case LabelAlignment::RIGHT:
                $labelX = $targetWidth - $labelBoxWidth - $labelMargin['r'];
                break;
            default:
                $labelX = intval($targetWidth / 2 - $labelBoxWidth / 2);
                break;
        }

        $labelY = $targetHeight - $labelMargin['b'];
        imagettftext($targetImage, $labelFontSize, 0, $labelX, $labelY, $foregroundColor, $labelFontPath, $label);

        return $targetImage;
    }

    private function imageToString($image): string
    {
        ob_start();
        imagepng($image);

        return (string) ob_get_clean();
    }

    public static function getContentType(): string
    {
        return 'image/png';
    }

    public static function getSupportedExtensions(): array
    {
        return ['png'];
    }

    public function getName(): string
    {
        return 'png';
    }
}
