<?php

namespace App\Modules\Portfolio\Enums;

use App\Support\LabelledEnum;

enum UsageType: string
{
    use LabelledEnum;

    case Apartment = 'apartment';
    case Studio = 'studio';
    case HolidayHome = 'holiday_home';
    case HolidayStudio = 'holiday_studio';
    case Commercial = 'commercial';
    case Parking = 'parking';
    case Storage = 'storage';
    case Other = 'other';

    protected static function translationGroup(): string
    {
        return 'usage_type';
    }
}
