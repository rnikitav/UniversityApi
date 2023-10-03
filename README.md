# University design practices (backend)

## Project preparation

### PHP 8.1

```shell
composer install
php artisan migrate
php artisan db:seed
php artisan key:generate
php artisan passport:keys --force
php artisan passport:client --password
```
