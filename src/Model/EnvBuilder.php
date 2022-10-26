<?php
namespace JaguarSoft\LaravelEnvLoader\Model;

use Illuminate\Database\Eloquent\Model;
use JaguarSoft\LaravelEnvLoader\Service\VarEnvDatabaseService;

class EnvBuilder {

    protected $Env;    
    protected $VarEnvService;

    protected $existe = false;

    public function __construct($Env = null) {
        $this->VarEnvService = new VarEnvDatabaseService(app());
        $this->Env = !is_null($Env) ? $Env : new Env;
        $this->existe = !is_null($Env);
        $this->noBloqueado();
        return $this;
    }

    public static function create($codigo) {
        return (new EnvBuilder)->codigo($codigo);
    }

    public static function delete($codigo) {
        Env::where('codigo',$codigo)->delete();
    }

    public static function find($codigo) {        
        $Env = Env::where('codigo',$codigo)->first();
        return (new EnvBuilder($Env))->codigo($codigo);
    }

    public static function ifNotExists($codigo, callable $callback){
        $Env = Env::where('codigo',$codigo)->first();        
        if(!$Env && !is_null($callback)){
            $EnvBuilder = (new EnvBuilder($Env))->codigo($codigo);
            $callback($EnvBuilder);
        };
    }

    public function codigo($codigo) {
        $this->Env->codigo = $codigo;
        return $this;
    }

    public function tipo($tipo) {
        $this->Env->tipo = $tipo;
        return $this;
    }

    public function tipoBoolean($valor = null) {
        return $this->tipo('boolean');
        if(!is_null($valor)){
            if(is_bool($valor)){
                $this->valor($valor ? 'true' : 'false');
            } else 
            if(is_int($valor)) {
                $this->valor($valor);
            }
        }
    }

    public function tipoString() {
        return $this->tipo('string');
    }

    public function valor($valor) {
        $this->Env->valor = $valor;
        return $this;
    }

    public function codigoPadre($codigoPadre) {
        $this->Env->codigoPadre = $codigoPadre;
        return $this;
    }

    public function noBloqueado() {
        $this->Env->bloqueado = 0;
        return $this;
    }

    public function bloqueado($bloqueado = 1) {
        $this->Env->bloqueado = $bloqueado;
        return $this;
    }

    public function comentario($comentario) {
        $this->Env->comentario = $comentario;
        return $this;
    }

    public function build() {
        return $this->Env;
    }

    public function saveNew() {        
        $Env = $this->VarEnvService->existe($this->Env->codigo);
        if(!$Env){
            $this->Env->save();
            return $this;
        } else {
            return (new EnvBuilder($Env));
        }
    }

    public function save() {
        return $this->existe ? $this->VarEnvService->actualizarEnv($this->Env) : $this->VarEnvService->crearEnv($this->Env);
    }

    public function trySave(callable $onError = null) {
        try {
            return $this->save();
        } catch(\Exception $e) {
            if(!is_null($onError)) {
                $onError($e->getMessage());
            }
        }
    }

    public function forceSave() {
        $this->Env->save();
        return $this->Env;
    }
}
