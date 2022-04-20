<?php
namespace JaguarSoft\LaravelEnvLoader\Model;

use JaguarSoft\LaravelEnvLoader\Model\VarEnv;
use JaguarSoft\LaravelEnvLoader\Model\Env;

class VarEnvBuilder {    
    public static function build(Env $env): VarEnv {
        $VarEnv = new VarEnv();
        $VarEnv->tipo = $env->tipo;
        $VarEnv->codigo = $env->codigo;
        $VarEnv->valor = $env->valor;
        $VarEnv->codigo_padre = $env->codigo_padre;
        $VarEnv->bloqueado = $env->bloqueado;
        $VarEnv->comentario = $env->comentario;
        return $VarEnv;
    }

}
