<?php

namespace App\Support;

use Picqer\Barcode\BarcodeGenerator;
use Picqer\Barcode\BarcodeGeneratorSVG;

class Barcode
{
    /**
     * Kode yang di-encode pada barcode (tanpa prefiks TRX- agar modulnya rapat & mudah discan).
     */
    public static function payload(string $code): string
    {
        $code = strtoupper(trim($code));

        return preg_match('/^TRX-(\d{8}-\d{3})$/', $code, $m) ? $m[1] : $code;
    }

    /**
     * Barcode Code 128 (SVG inline) berisi kode booking, bisa discan petugas.
     */
    public static function code128(string $value, string $color = '#0B3A22'): string
    {
        $value = strtoupper(trim($value));

        if ($value === '') {
            return '';
        }

        $generator = new BarcodeGeneratorSVG;

        $svg = $generator->getBarcode(
            $value,
            BarcodeGenerator::TYPE_CODE_128,
            1.5,
            40,
            $color
        );

        return self::inline($svg);
    }

    /**
     * Buang prolog XML/DOCTYPE dan buat SVG mengikuti lebar container.
     */
    private static function inline(string $svg): string
    {
        $svg = (string) preg_replace('/<\?xml.*?\?>\s*/s', '', $svg);
        $svg = (string) preg_replace('/<!DOCTYPE.*?>\s*/s', '', $svg);
        $svg = (string) preg_replace(
            '/<svg /',
            '<svg style="width:100%;height:auto;display:block" preserveAspectRatio="xMidYMid meet" ',
            $svg,
            1
        );

        return trim($svg);
    }
}
