<?php
require_once 'config.php';
require_once 'write-sheet.php';

create_spreadsheet();
  
function create_spreadsheet() {
  
    $client = new Google_Client();
    $client->setScopes([Google_Service_Sheets::SPREADSHEETS,Google_Service_Drive::DRIVE]);
 
 
    $db = new DB();
  
    $arr_token = (array) $db->get_access_token();
    $accessToken = array(
        'access_token' => $arr_token['access_token'],
        'expires_in' => $arr_token['expires_in'],
    );
  
    $client->setAccessToken($accessToken);
  
    $service = new Google_Service_Sheets($client);
  
    try {
        $spreadsheet = new Google_Service_Sheets_Spreadsheet([
            'properties' => [
                'title' => 'Weather reporting data'
            ]
        ]);
        $spreadsheet = $service->spreadsheets->create($spreadsheet, [
            'fields' => 'spreadsheetId'
        ]);
        write_to_sheet($spreadsheet->spreadsheetId);

        $users = unserialize (EMAILS);
        $service = new Google_Service_Drive($client);
        $permission = new Google_Service_Drive_Permission();
        $permission->setRole( 'reader' );
        $permission->setType( 'anyone' );
        $service->permissions->create( $spreadsheet->spreadsheetId, $permission );

        foreach ($users as $user){

            $permission->setRole( 'reader' );
            $permission->setType( 'user' );
            $permission->setEmailAddress($user);
            $service->permissions->create( $spreadsheet->spreadsheetId, $permission );
        }
       
    } catch(Exception $e) {
        if( 401 == $e->getCode() ) {
            $refresh_token = $db->get_refersh_token();
  
            $client = new GuzzleHttp\Client(['base_uri' => 'https://accounts.google.com']);
  
            $response = $client->request('POST', '/o/oauth2/token', [
                'form_params' => [
                    "grant_type" => "refresh_token",
                    "refresh_token" => $refresh_token,
                    "client_id" => GOOGLE_CLIENT_ID,
                    "client_secret" => GOOGLE_CLIENT_SECRET,
                ],
            ]);
  
            $data = (array) json_decode($response->getBody());
            $data['refresh_token'] = $refresh_token;
  
            $db->update_access_token(json_encode($data));
  
            create_spreadsheet();
        } else {
            echo $e->getMessage(); //print the error just in case your sheet is not created.
        }
    }
}
