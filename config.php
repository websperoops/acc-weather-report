<?php
require_once 'vendor/autoload.php';
require_once 'class-db.php';
  
define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');
define('LOCATION_URL', 'PASTE_ACCU_WEATHER_LOCATION_API_URL_WITH__API_KEY');
define ('CURRENT_CONDITION_URL', 'PASTE_ACCU_WEATHER_CURRENT_CONDITION_API_URL_WITH_API_KEY');

define ("EMAILS", serialize (array ('EMAIL_ADDRESS_OF_USER')));

$config = [
    'callback' => 'YOUR_PROJECT_CALLBACK_URL',
    'keys'     => [
                    'id' => GOOGLE_CLIENT_ID,
                    'secret' => GOOGLE_CLIENT_SECRET
                ],
    'scope'    => 'https://www.googleapis.com/auth/spreadsheets',
    'scope'    => 'https://www.googleapis.com/auth/drive',
    'scope'    => 'https://www.googleapis.com/auth/drive.file',
    'authorize_url_parameters' => [
            'approval_prompt' => 'force', // to pass only when you need to acquire a new refresh token.
            'access_type' => 'offline'
    ]
];

  
$adapter = new Hybridauth\Provider\Google( $config );