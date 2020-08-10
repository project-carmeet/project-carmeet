# Project carmeet

## Getting the project running
### Requirements
 - PHP 7.2 or higher with the pdo_mysql extension enabled.
 - Composer
 - Symfony cli
 - Mysql server 5.6 or higher

##### OR
 - Docker

### Start using local php installation
```shell script
composer install
php bin/serve
```

### Start using docker
```shell script
docker-compose up -d
docker-compose exec php composer install
docker-compose exec php bin/console dev:load
```
