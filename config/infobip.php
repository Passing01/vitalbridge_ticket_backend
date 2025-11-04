<?php

return [
    'base_url' => env('INFOBIP_BASE_URL', 'https://d9pg6l.api.infobip.com'),
    'api_key' => env('INFOBIP_API_KEY'),
    'sender_id' => env('INFOBIP_SENDER_ID', 'VitalBridge_Ticket'),
    'default_country_code' => '226', // Code pays par défaut (Burkina Faso)
];
