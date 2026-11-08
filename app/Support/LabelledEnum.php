<?php

namespace App\Support;

/**
 * Gemeinsame Helfer für String-Enums mit übersetzten Bezeichnungen.
 * Übersetzungsschlüssel: enums.<kurzname>.<wert>
 */
trait LabelledEnum
{
    abstract protected static function translationGroup(): string;

    public function label(): string
    {
        return __('enums.'.static::translationGroup().'.'.$this->value);
    }

    /** @return array<string, string> value => label */
    public static function options(): array
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[$case->value] = $case->label();
        }

        return $out;
    }

    /** @return list<string> */
    public static function values(): array
    {
        return array_map(fn (self $c) => $c->value, self::cases());
    }
}
