# Laravel Env Loader

This package allows you to load env variables from external sources like database or yaml file.

## Installation

```bash
composer require jaguarsoft/laravel-env-loader
```
**For Laravel 5**
```bash
"jaguarsoft/laravel-env-loader": "^5.2"
```

**For Laravel 6**
```bash
"jaguarsoft/laravel-env-loader": "^6"
```

## Use

**Mirations for tb_envs table**
```ini
php artisan vendor:publish --provider="JaguarSoft\LaravelEnvLoader\EnvLoaderServiceProvider"
```

**Autodiscover**
```ini
php artisan package:discover
```
