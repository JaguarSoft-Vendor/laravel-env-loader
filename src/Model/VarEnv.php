<?php
namespace JaguarSoft\LaravelEnvLoader\Model;

use JaguarSoft\LaravelEnvLoader\DotEnvLoader;

class VarEnv 
{
    public $tipo;
    public $codigo;
    public $valor;
    public $codigo_padre;
    public $padre;
    public $bloqueado = true;
    public $comentario = '';

    public static function from($codigo, $valor) {
        $VarEnv = new VarEnv();
        $VarEnv->codigo = $codigo;
        $VarEnv->valor = $valor;
        return $VarEnv;
    }

    public function val() {
        $valor = $this->valor;
        $DotEnvLoader = new DotEnvLoader(null);
        //$valor = is_string($valor) ? $DotEnvLoader->normaliseVariable($this->codigo, $valor) : $valor;
        $tipo = strtolower($this->tipo);
        switch($tipo) {
            case 'integer':
                return (int) $valor;
                break;
            case 'boolean':
                return (bool) $valor;
                break;
            case 'float':
                return (float) $valor;
                break;
            case 'string':
                return (string) $valor;
                break;
            case 'json':                
                if(substr($valor,0,1) == "'" && substr($valor,strlen($valor)-1,1)== "'") {
                    $valor = substr($valor,1,strlen($valor)-2);
                }                
                return json_decode($valor, true);
                break;
            default:
                return $valor;
        }
    }
}
