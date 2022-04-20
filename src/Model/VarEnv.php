<?php
namespace JaguarSoft\LaravelEnvLoader\Model;

class VarEnv 
{
    public $tipo;
    public $codigo;
    public $valor;
    public $codigo_padre;
    public $padre;
    public $bloqueado = true;

    public static function from($codigo, $valor) {
        $VarEnv = new VarEnv();
        $VarEnv->codigo = $codigo;
        $VarEnv->valor = $valor;
        return $VarEnv;
    }

    public function val() {
        $tipo = strtolower($this->tipo);
        switch($tipo) {
            case 'integer':
                return (int) $this->valor;
                break;
            case 'boolean':
                return (bool) $this->valor;
                break;
            case 'float':
                return (float) $this->valor;
                break;
            case 'string':
                return (string) $this->valor;
                break;
            default:
                return $this->valor;
        }
    }
}
