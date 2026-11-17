<?php

namespace App\Modules\Parties\Enums;

use App\Support\LabelledEnum;

enum PartyKind: string
{
    use LabelledEnum;

    case Person = 'person';
    case Company = 'company';

    protected static function translationGroup(): string
    {
        return 'party_kind';
    }
}
