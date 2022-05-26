<?php
    require_once 'config.php';

    $client = new Google_Client();
    $client->setScopes([Google_Service_Sheets::SPREADSHEETS,Google_Service_Drive::DRIVE]);
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLE_REDIRECT_URL);
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    $authUrl = $client->createAuthUrl();

    header('Location: '.$authUrl);
