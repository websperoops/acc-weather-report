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

###### For generating report manually from command line
```
php -f index.php
```

###### Command for cron job
```
php -f path_to_your_folder/create-sheet.php >/dev/null 2>&1
```
