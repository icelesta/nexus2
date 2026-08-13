<?php

declare(strict_types=1);

namespace App\Support\Barcode;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrGenerator
{
    /**
     * Build QR payload from Item Code + Item Name.
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
    

    public static function item(
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
     * Normalize QR payload.
     */
    public static function normalize(
        string $payload,
    ): string {

        return strtoupper(
            Str::of($payload)
                ->replaceMatches('/[^A-Z0-9]+/', '-')
                ->trim('-')
                ->toString()
        );
    }

    /**
     * Generate SVG QR Code.
     */
    public static function svg(
        string $payload,
        int $size = 180,
        int $margin = 1,
    ): HtmlString {

        $payload = trim($payload);

        if ($payload === '') {
            return new HtmlString('');
        }

        return QrCode::size($size)
            ->margin($margin)
            ->generate($payload);
    }

    /**
     * Generate PNG QR Code.
     */
    public static function png(
        string $payload,
        int $size = 300,
    ): string {

        $payload = trim($payload);

        if ($payload === '') {
            return '';
        }

        return base64_encode(
            QrCode::format('png')
                ->size($size)
                ->margin(1)
                ->generate($payload)
        );
    }

    /**
     * Generate PNG Data URI.
     */
    public static function pngDataUri(
        string $payload,
        int $size = 300,
    ): string {

        $png = self::png($payload, $size);

        if ($png === '') {
            return '';
        }

        return 'data:image/png;base64,' . $png;
    }
}