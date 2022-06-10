<?php
error_reporting(E_NOTICE);

class getData
{      
	const LOCATION_URL = 'PASTE_ACCU_WEATHER_LOCATION_API_URL_WITH__API_KEY';
	const CURRENT_CONDITION_URL = 'PASTE_ACCU_WEATHER_CURRENT_CONDITION_API_URL_WITH_API_KEY';
	const SPREADSHEET_ID = 'SPREADSHEET_ID';
	const SPREADSHEET_RANGE =  'SPREADSHEET_RANGE';
	const VALUE_OPTIONS =  'USER_ENTERED';
	const PUBLIC_URL =  'SPREADSHEET_PUBLIC_URL';
	const EMAILS = 'COMMA_SEPARATED_USER_EMAILS';
	
	public function getAccuWeatherData(){
		require __DIR__ . '/vendor/autoload.php';
		$client = new \Google_Client();
		$client->setApplicationName('Google Sheets and PHP');
		$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
		$client->setAccessType('offline');
		$client->setAuthConfig(__DIR__ . '/credentials.json');
		$service = new Google_Service_Sheets($client);

		$requestBody = new Google_Service_Sheets_ClearValuesRequest();
		$response = $service->spreadsheets_values->clear(self::SPREADSHEET_ID, self::SPREADSHEET_RANGE, $requestBody);

		$data = ['Name','Country', 'Region','Timezone', 'Rank', 'Latitude', 'Longitude', 'Weather Text', 'Is Day Time', 'Temperature Celsius (C)', 'Temperature Fahrenheit (F)', 'Last Updated At'];
		
		$values = [
			$data,
		];
		$body = new Google_Service_Sheets_ValueRange([
			'values' => [$data]
		]);
		$params = [
			'valueInputOption' => self::VALUE_OPTIONS
		];
		$service->spreadsheets_values->append(self::SPREADSHEET_ID, self::SPREADSHEET_RANGE, $body, $params);
		$this->writeReportData();

		$msg = "You can check acc weather report by click on below url \n";
		$msg .= self::PUBLIC_URL;
		$msg = wordwrap($msg,70);
	    mail(self::EMAILS,"Weather Report",$msg);
	}
	
	private function writeReportData(){		
		require __DIR__ . '/vendor/autoload.php';
		$client = new \Google_Client();
		$client->setApplicationName('Google Sheets and PHP');
		$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
		$client->setAccessType('offline');
		$client->setAuthConfig(__DIR__ . '/credentials.json');
		$service = new Google_Service_Sheets($client);

		$locationData = $this->makeCurlCall(self::LOCATION_URL);
		$currentConditionData = $this->makeCurlCall(self::CURRENT_CONDITION_URL);
	
		$locationArray = json_decode($locationData, true);
		$currentConditionArray = json_decode($currentConditionData, true);
		$result = [];
		
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
				$currentConditionArray[$key]['LocalObservationDateTime'].'||';
				$time = strtotime($currentConditionArray[$key]['LocalObservationDateTime']);
                date_default_timezone_set($arr['timezone']);
                $dateInLocal = date("d/m/y h:i a T", $time);
                $arr['last_updated_at'] =  $dateInLocal;

				$body = new Google_Service_Sheets_ValueRange([
					'values' => [  array_values($arr)]
				]);
				$params = [
					'valueInputOption' => self::VALUE_OPTIONS
				];
				$res = $service->spreadsheets_values->append(self::SPREADSHEET_ID, self::SPREADSHEET_RANGE, $body, $params);
			}
		}
		return	$res;
	}

	private function makeCurlCall(string $url){
		try {
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			$response = curl_exec($curl);
			curl_close($curl);
			return $response;
		}
		catch(Exception $e) {
			return $e->getMessage();
		}
	}
} 

$myobj = new getData;
$res = $myobj->getAccuWeatherData();

echo "Report Generated Successfully";


