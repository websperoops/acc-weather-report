<?php
require_once 'config.php';

try {
    $client = new Google_Client();
    $client->setScopes([Google_Service_Sheets::SPREADSHEETS,Google_Service_Drive::DRIVE]);
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLE_REDIRECT_URL);
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    if(isset($_GET["code"])){
        $authCode = $_GET["code"];
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
        $db = new DB();
        $db->update_access_token(json_encode($accessToken));
        echo "Access token inserted successfully.";
    }
}
catch( Exception $e ){
    echo $e->getMessage() ;
}
