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
    public $comentario = '';

    public static function from($codigo, $valor) {
        $VarEnv = new VarEnv();
        $VarEnv->codigo = $codigo;
        $VarEnv->valor = $valor;
        return $VarEnv;
    }

    public function val() {
        $valor = $this->valor;        
        $tipo = strtolower($this->tipo);
        switch($tipo) {
            case 'integer':
                return (int) $valor;
                break;
            case 'boolean':
                switch($valor) {
                    case 'true': 
                    case 'si':
                    case '1':
                        return true; 
                        break;
                    case 'false':
                    case 'no':
                    case '0':
                        return false;
                        break;
                    default:
                        return (bool) $valor;                        
                }            
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
