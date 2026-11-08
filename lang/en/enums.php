<?php

return [
    'ownership_type' => ['sole' => 'Sole ownership', 'co_ownership' => 'Co-ownership', 'condominium' => 'Condominium ownership', 'other' => 'Other'],
    'property_type' => [
        'single_family_house' => 'Single-family house', 'multi_family_house' => 'Multi-family house',
        'condominium_apartment' => 'Condominium apartment', 'holiday_house' => 'Holiday house', 'other' => 'Other',
    ],
    'usage_type' => [
        'apartment' => 'Apartment', 'studio' => 'Studio', 'holiday_home' => 'Holiday home', 'holiday_studio' => 'Holiday studio',
        'commercial' => 'Commercial', 'parking' => 'Parking / garage', 'storage' => 'Storage / cellar', 'other' => 'Other',
    ],
    'occupancy_type' => ['rental' => 'Rented', 'owner_occupied' => 'Owner-occupied', 'private_use' => 'Private holiday use', 'vacant' => 'Vacant'],
    'party_kind' => ['person' => 'Person', 'company' => 'Company'],
    'party_role' => ['tenant' => 'Tenant', 'owner' => 'Owner', 'supplier' => 'Supplier / service provider', 'other' => 'Other'],
];
