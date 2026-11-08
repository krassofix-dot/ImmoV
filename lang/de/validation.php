<?php

/*
 * Deutsche Validierungsmeldungen (Auswahl der verwendeten Regeln).
 * Fehlende Schlüssel fallen auf Englisch zurück.
 */
return [
    'accepted' => ':attribute muss akzeptiert werden.',
    'after_or_equal' => ':attribute muss ein Datum nach oder gleich :date sein.',
    'alpha' => ':attribute darf nur Buchstaben enthalten.',
    'array' => ':attribute muss eine Liste sein.',
    'before_or_equal' => ':attribute muss ein Datum vor oder gleich :date sein.',
    'between' => [
        'numeric' => ':attribute muss zwischen :min und :max liegen.',
        'string' => ':attribute muss zwischen :min und :max Zeichen lang sein.',
    ],
    'boolean' => ':attribute muss wahr oder falsch sein.',
    'confirmed' => 'Die Bestätigung von :attribute stimmt nicht überein.',
    'current_password' => 'Das Passwort ist falsch.',
    'date' => ':attribute muss ein gültiges Datum sein.',
    'decimal' => ':attribute muss eine Zahl mit :decimal Nachkommastellen sein.',
    'email' => ':attribute muss eine gültige E-Mail-Adresse sein.',
    'enum' => 'Der gewählte Wert für :attribute ist ungültig.',
    'exists' => 'Der gewählte Wert für :attribute ist ungültig.',
    'in' => 'Der gewählte Wert für :attribute ist ungültig.',
    'integer' => ':attribute muss eine ganze Zahl sein.',
    'max' => [
        'numeric' => ':attribute darf nicht grösser als :max sein.',
        'string' => ':attribute darf höchstens :max Zeichen lang sein.',
    ],
    'min' => [
        'numeric' => ':attribute muss mindestens :min sein.',
        'string' => ':attribute muss mindestens :min Zeichen lang sein.',
    ],
    'numeric' => ':attribute muss eine Zahl sein.',
    'password' => [
        'letters' => ':attribute muss mindestens einen Buchstaben enthalten.',
        'mixed' => ':attribute muss Gross- und Kleinbuchstaben enthalten.',
        'numbers' => ':attribute muss mindestens eine Ziffer enthalten.',
        'symbols' => ':attribute muss mindestens ein Sonderzeichen enthalten.',
        'uncompromised' => ':attribute ist in einem Datenleck aufgetaucht. Bitte ein anderes wählen.',
    ],
    'required' => ':attribute ist erforderlich.',
    'required_if' => ':attribute ist erforderlich, wenn :other :value ist.',
    'required_with' => ':attribute ist erforderlich, wenn :values angegeben ist.',
    'size' => [
        'string' => ':attribute muss genau :size Zeichen lang sein.',
    ],
    'string' => ':attribute muss ein Text sein.',
    'unique' => ':attribute ist bereits vergeben.',

    'attributes' => [
        'name' => 'Bezeichnung', 'email' => 'E-Mail', 'password' => 'Passwort', 'current_password' => 'Aktuelles Passwort',
        'country_id' => 'Land', 'code' => 'Ländercode', 'name_de' => 'Name Deutsch', 'name_en' => 'Name Englisch',
        'default_currency' => 'Währung', 'currency' => 'Währung', 'purchase_currency' => 'Währung', 'purchase_price' => 'Kaufpreis',
        'purchase_date' => 'Kaufdatum', 'construction_year' => 'Baujahr', 'plot_area_m2' => 'Grundstücksfläche', 'area_m2' => 'Fläche',
        'rooms' => 'Zimmer', 'floors' => 'Geschosse', 'first_name' => 'Vorname', 'last_name' => 'Nachname', 'company_name' => 'Firma',
        'kind' => 'Typ', 'role' => 'Rolle', 'type' => 'Art', 'party_id' => 'Partei', 'start_date' => 'Beginn', 'end_date' => 'Ende',
        'rent_net' => 'Nettomiete', 'advance_payment' => 'Akonto', 'locale' => 'Sprache', 'code_2fa' => 'Code',
    ],
    'values' => [
        'kind' => ['person' => 'Person', 'company' => 'Firma'],
    ],
];
