<?php
require_once 'config.php';
  
  
function write_to_sheet($spreadsheetId = '') {
  
    $client = new Google_Client();
  
    $db = new DB();
  
    $arr_token = (array) $db->get_access_token();
    $accessToken = array(
        'access_token' => $arr_token['access_token'],
        'expires_in' => $arr_token['expires_in'],
    );
  
    $client->setAccessToken($accessToken);
  
    $service = new Google_Service_Sheets($client);
  
    try {
        $locationData = makeCurlCall(LOCATION_URL);
		$currentConditionData = makeCurlCall(CURRENT_CONDITION_URL);

		$locationArray = json_decode($locationData, true);
		$currentConditionArray = json_decode($currentConditionData, true);
		$result = [];

        $range = 'Sheet1';
       
        $data = ['Name','Country', 'Region','Timezone', 'Rank', 'Latitude', 'Longitude', 'Weather Text', 'Is Day Time', 'Temperature Celsius (C)', 'Temperature Fahrenheit (F)', 'Last Updated At'];
        
        
        $values = [
            $data,
        ];
        $body = new Google_Service_Sheets_ValueRange([
            'values' => [$data]
        ]);
        $params = [
            'valueInputOption' => 'USER_ENTERED'
        ];
        $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);

        foreach($locationArray as $key => $value){
            $arr['name'] = $value['LocalizedName'];
			$arr['country'] = $value['Country']['LocalizedName'];
			$arr['region'] = $value['Region']['LocalizedName'];
			$arr['timezone'] = $value['TimeZone']['Name'];
			$arr['rank'] = $value['Rank'];

		
			if($value['LocalizedName'] == $currentConditionArray[$key]['LocalizedName']) {
				$arr['latitude'] = $currentConditionArray[$key]['GeoPosition']['Latitude'];
				$arr['longitude'] = $currentConditionArray[$key]['GeoPosition']['Longitude'];
				$arr['weather_text'] = $currentConditionArray[$key]['WeatherText'];
				$arr['is_day_time'] = $currentConditionArray[$key]['IsDayTime'];
				$arr['temperature_celsius'] =  $currentConditionArray[$key]['Temperature']['Metric']['Value'];
				$arr['temperature_fahrenheit'] = $currentConditionArray[$key]['Temperature']['Imperial']['Value'];
				$time = strtotime($currentConditionArray[$key]['LocalObservationDateTime']);
                date_default_timezone_set($arr['timezone']);
                $dateInLocal = date("d/m/y h:i a T", $time);
                $arr['last_updated_at'] =  $dateInLocal;
            }
          
            $values = [
                array_values($arr)
            ];
            $body = new Google_Service_Sheets_ValueRange([
                'values' => $values
            ]);
        
            $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);	
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
  
            write_to_sheet($spreadsheetId);
        } else {
            echo $e->getMessage(); //print the error just in case your data is not added.
        }
    }
}
function makeCurlCall(string $url){

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($curl);
    curl_close($curl);
    $response = curl_exec($curl);
    return $response;
    
}