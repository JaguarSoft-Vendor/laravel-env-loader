<?php 
namespace JaguarSoft\LaravelEnvLoader\Provider;

use Illuminate\Support\ServiceProvider;
use Illuminate\Container\Container;

use JaguarSoft\LaravelEnvLoader\Business\VarEnvBusiness;
use JaguarSoft\LaravelEnvLoader\Contract\VarEnvService;
use JaguarSoft\LaravelEnvLoader\Service\VarEnvDatabaseService;
use JaguarSoft\LaravelEnvLoader\Service\VarEnvYamlService;
use JaguarSoft\LaravelEnvLoader\Business\JaguarVarBusiness;

class AppBindProvider extends ServiceProvider {
    
    public function register()
    {
        $this->app->bind(
            'JaguarSoft\LaravelEnvLoader\Contract\VarEnvService',
            [
            'yaml' => 'JaguarSoft\LaravelEnvLoader\Service\VarEnvYamlService',
            'database' => 'JaguarSoft\LaravelEnvLoader\Service\VarEnvDatabaseService'
            ][env('VARENV_SERVICE','database')]
        );

        $this->app->singleton('JaguarVarBusiness', function(Container $container){        
            return $container->make(JaguarVarBusiness::class);
        });
    }

    public function boot() 
    {
    }
}