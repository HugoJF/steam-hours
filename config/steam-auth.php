<?php

return [

    /*
     * Redirect URL after login
     */
    'redirect_url' => '/auth/steam/handle',
    /*
     * API Key (set in .env file) [http://steamcommunity.com/dev/apikey]
     */
    'api_key' => env('STEAM_APIKEY', ''),
    /*
     * Is using https ?
     */
    'https' => false

];
