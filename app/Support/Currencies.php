<?php

namespace App\Support;

/** Unterstützte Währungen (ISO 4217). Erweiterbar. */
final class Currencies
{
    public const CODES = ['CHF', 'EUR'];

    /** @return array<string, string> */
    public static function options(): array
    {
        return array_combine(self::CODES, self::CODES);
    }

    /** Formatiert einen gespeicherten Dezimalwert ohne Float-Umweg, z. B. 1'234.50 CHF */
    public static function format(?string $amount, ?string $currency): string
    {
        if ($amount === null || $amount === '') {
            return '–';
        }

        $negative = str_starts_with($amount, '-');
        [$int, $dec] = array_pad(explode('.', ltrim($amount, '-')), 2, '');
        $dec = str_pad(substr($dec, 0, 2), 2, '0');
        $int = strrev(implode("'", str_split(strrev($int), 3)));

        return trim(($negative ? '-' : '').$int.'.'.$dec.' '.$currency);
    }
}
