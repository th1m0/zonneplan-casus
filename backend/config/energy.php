<?php

declare(strict_types=1);

return [
    'api_base_url' => env('ZONNEPLAN_API_BASE_URL', ''),
    'api_key' => env('ZONNEPLAN_API_KEY', ''),

    // Cache settings (for future implementation)
    'cache_ttl' => env('ZONNEPLAN_CACHE_TTL', 3600), // 1 hour
    'cache_enabled' => env('ZONNEPLAN_CACHE_ENABLED', false),
];
