# acc-weather-report

# INTRODUCTION
###### It will generate weather Report of 50 top cities based on Location and their current Condition

# Prerequisites
```
PHP >= 7
MySql
Composer
```
# INSTALLATION
```
git pull
```
```
cd acc-weather-report
```
```
Composer install
```
```
Create Database
```
### Run below query in sql
```
CREATE TABLE `google_oauth` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `provider` varchar(255) NOT NULL,
 `provider_value` text NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```
### Create credentials.json Replace credentials in credentials.json file
```
{

}
```

### Change db settings in class-db.php

### Access set-token.php file via browser to create auth token

### For creating report call create-sheet.php file
###### For generation report manually from command line
```
php -f create-sheet.php
```

###### Command for cron job
```
php -f path_to_your_folder/create-sheet.php >/dev/null 2>&1
```
