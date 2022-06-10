# weather-report

# INTRODUCTION
###### It will generate weather Report of 50 top cities based on Location and their current Condition

# Prerequisites
```
PHP >= 7
Composer
```
# INSTALLATION
```
git pull
```
```
cd weather-report
```
```
Composer install
```

### Create a project on google cloud platform, enable google sheet and drive api and  get service account credentials from there
### Create credentials.json Replace credentials in credentials.json file
```
{

}
```
### Create an accu weather account and get api key
### Create Google Spreadsheet and make it public accesible copy Spreadsheet Id, Range, public url
### Replace Constants in index.php file 
```
{
	LOCATION_URL
	CURRENT_CONDITION_URL
	SPREADSHEET_ID
	SPREADSHEET_RANGE
	PUBLIC_URL
	EMAILS
}
```

###### For generating report manually from command line
```
php -f index.php
```

###### Command for cron job
```
php -f path_to_your_folder/index.php >/dev/null 2>&1
```
