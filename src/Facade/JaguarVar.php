<?php 
namespace JaguarSoft\LaravelEnvLoader\Facade;

class JaguarVar extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'JaguarVarBusiness';
    }
}
