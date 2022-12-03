# Laravel Env Loader

This package allows you to load env variables from external sources like database or yaml file.

## Installation

```bash
composer require jaguarsoft/laravel-debugbar
```

## Use

**Mirations for tb_envs table**
```ini
php artisan vendor:publish --provider="JaguarSoft\LaravelEnvLoader\EnvLoaderServiceProvider"
```