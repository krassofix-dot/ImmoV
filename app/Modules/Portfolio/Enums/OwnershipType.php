<?php

namespace App\Modules\Portfolio\Enums;

use App\Support\LabelledEnum;

enum OwnershipType: string
{
    use LabelledEnum;

    case Sole = 'sole';
    case CoOwnership = 'co_ownership';
    case Condominium = 'condominium';
    case Other = 'other';

    protected static function translationGroup(): string
    {
        return 'ownership_type';
    }
}
