<?php

namespace App\Modules\Parties\Enums;

use App\Support\LabelledEnum;

enum PartyRole: string
{
    use LabelledEnum;

    case Tenant = 'tenant';
    case Owner = 'owner';
    case Supplier = 'supplier';
    case Other = 'other';

    protected static function translationGroup(): string
    {
        return 'party_role';
    }
}
