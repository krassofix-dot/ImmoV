<?php

namespace App\Modules\Portfolio\Enums;

use App\Support\LabelledEnum;

enum PropertyType: string
{
    use LabelledEnum;

    case SingleFamilyHouse = 'single_family_house';
    case MultiFamilyHouse = 'multi_family_house';
    case CondominiumApartment = 'condominium_apartment';
    case HolidayHouse = 'holiday_house';
    case Other = 'other';

    protected static function translationGroup(): string
    {
        return 'property_type';
    }
}
