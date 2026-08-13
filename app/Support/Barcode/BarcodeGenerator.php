<?php

declare(strict_types=1);

namespace App\Support\Barcode;

use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Illuminate\Support\Str;

class BarcodeGenerator
{
    /**
     * Supported PNG barcode types.
     */
    private const PNG_TYPES = [
        'CODE128' => BarcodeGeneratorPNG::TYPE_CODE_128,
        'CODE39'  => BarcodeGeneratorPNG::TYPE_CODE_39,
        'EAN13'   => BarcodeGeneratorPNG::TYPE_EAN_13,
        'EAN8'    => BarcodeGeneratorPNG::TYPE_EAN_8,
    ];

    /**
     * Supported SVG barcode types.
     */
    private const SVG_TYPES = [
        'CODE128' => BarcodeGeneratorSVG::TYPE_CODE_128,
        'CODE39'  => BarcodeGeneratorSVG::TYPE_CODE_39,
        'EAN13'   => BarcodeGeneratorSVG::TYPE_EAN_13,
        'EAN8'    => BarcodeGeneratorSVG::TYPE_EAN_8,
    ];

    /**
     * Generate barcode string from Item Code + Item Name.
     *
     * Example:
     *
     * ITEM-000001
     * BALL VALVE 2 INCH
     *
     * =>
     *
     * ITEM-000001-BALL-VALVE-2-INCH
     */
    public static function make(
        string $itemCode,
        string $itemName = '',
    ): string {

        $itemCode = strtoupper(trim($itemCode));

        $itemName = strtoupper(trim($itemName));

        if ($itemCode === '') {
            return '';
        }

        if ($itemName === '') {
            return $itemCode;
        }

        $itemName = Str::of($itemName)
            ->replaceMatches('/[^A-Z0-9]+/', '-')
            ->trim('-')
            ->toString();

        return "{$itemCode}-{$itemName}";
    }

    /**
     * Generate PNG barcode (Base64).
     */
    public static function png(
        string $value,
        string $type = 'CODE128',
        int $widthFactor = 2,
        int $height = 60,
    ): string {

        $value = trim($value);

        if ($value === '') {
            return '';
        }

        $generator = new BarcodeGeneratorPNG();

        $barcodeType = self::PNG_TYPES[$type]
            ?? self::PNG_TYPES['CODE128'];

        return base64_encode(
            $generator->getBarcode(
                $value,
                $barcodeType,
                $widthFactor,
                $height,
            ),
        );
    }

    /**
     * Generate PNG Data URI.
     */
    public static function pngDataUri(
        string $value,
        string $type = 'CODE128',
    ): string {

        $png = self::png($value, $type);

        if ($png === '') {
            return '';
        }

        return 'data:image/png;base64,' . $png;
    }

    /**
     * Generate SVG barcode.
     */
    public static function svg(
        string $value,
        string $type = 'CODE128',
        int $widthFactor = 2,
        int $height = 60,
    ): string {

        $value = trim($value);

        if ($value === '') {
            return '';
        }

        $generator = new BarcodeGeneratorSVG();

        $barcodeType = self::SVG_TYPES[$type]
            ?? self::SVG_TYPES['CODE128'];

        return $generator->getBarcode(
            $value,
            $barcodeType,
            $widthFactor,
            $height,
        );
    }

    /**
     * Normalize barcode string.
     */
    public static function normalize(
        string $barcode,
    ): string {

        return strtoupper(
            Str::of($barcode)
                ->replaceMatches('/[^A-Z0-9]+/', '-')
                ->trim('-')
                ->toString()
        );
    }
}