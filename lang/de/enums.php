<?php

return [
    'ownership_type' => ['sole' => 'Alleineigentum', 'co_ownership' => 'Miteigentum', 'condominium' => 'Stockwerkeigentum', 'other' => 'Andere'],
    'property_type' => [
        'single_family_house' => 'Einfamilienhaus', 'multi_family_house' => 'Mehrfamilienhaus',
        'condominium_apartment' => 'Eigentumswohnung (StWE)', 'holiday_house' => 'Ferienhaus', 'other' => 'Andere',
    ],
    'usage_type' => [
        'apartment' => 'Wohnung', 'studio' => 'Studio', 'holiday_home' => 'Ferienhaus', 'holiday_studio' => 'Ferien-Studio',
        'commercial' => 'Gewerbe', 'parking' => 'Parkplatz / Garage', 'storage' => 'Lager / Keller', 'other' => 'Andere',
    ],
    'occupancy_type' => ['rental' => 'Vermietet', 'owner_occupied' => 'Eigennutzung', 'private_use' => 'Private Feriennutzung', 'vacant' => 'Leerstand'],
    'party_kind' => ['person' => 'Person', 'company' => 'Firma'],
    'party_role' => ['tenant' => 'Mieter/in', 'owner' => 'Eigentümer/in', 'supplier' => 'Lieferant / Dienstleister', 'other' => 'Andere'],
];
