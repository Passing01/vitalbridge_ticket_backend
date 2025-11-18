<?php

return [
    'api_key' => env('IKODDI_API_KEY'),
    'group_id' => env('IKODDI_GROUP_ID'),
    'otp_app_id' => env('IKODDI_OTP_APP_ID'),
    // Base host for IKODDI API; full paths like /api/v1/groups/{groupId}/otp/{otpAppId}/{type}/{identity} will be appended in code
    'api_base_url' => env('IKODDI_API_BASE_URL', 'https://api.staging.ikoddi.com'),
];
