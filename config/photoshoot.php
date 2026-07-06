<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Photoshoot API page size
    |--------------------------------------------------------------------------
    |
    | Number of photoshoot records returned per page by the
    | get_photoshoot_by_category_id API. Configured via the .env value
    | PHOTOSHOOT_API_PER_PAGE so it can be tuned without code changes.
    |
    */
    'per_page' => (int) env('PHOTOSHOOT_API_PER_PAGE', 20),

    /*
    |--------------------------------------------------------------------------
    | Country options
    |--------------------------------------------------------------------------
    |
    | The dropdown shows the uppercase code (label) in this exact order, but
    | the value saved to the database is always the lowercase code.
    |
    */
    'countries' => [
        'ph' => 'PH',
        'br' => 'BR',
        'mx' => 'MX',
        'ar' => 'AR',
        'in' => 'IN',
    ],

];
