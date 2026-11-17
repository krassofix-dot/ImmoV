<?php

namespace App\Modules\Parties\Enums;

use App\Support\LabelledEnum;

enum OccupancyType: string
{
    use LabelledEnum;

    case Rental = 'rental';
    case OwnerOccupied = 'owner_occupied';
    case PrivateUse = 'private_use';
    case Vacant = 'vacant';

    protected static function translationGroup(): string
    {
        return 'occupancy_type';
    }
}
