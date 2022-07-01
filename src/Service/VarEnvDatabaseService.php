<?php 
namespace JaguarSoft\LaravelEnvLoader\Service;

use JaguarSoft\LaravelEnvLoader\Model\VarEnv;
use JaguarSoft\LaravelEnvLoader\Contract\VarEnvService;
use JaguarSoft\LaravelEnvLoader\DotEnvLoader;
use JaguarSoft\LaravelEnvLoader\Model\Env as Model;
use JaguarSoft\LaravelEnvLoader\Model\VarEnvBuilder;

use Illuminate\Contracts\Foundation\Application;
use Exception;

class VarEnvDatabaseService implements VarEnvService {	
	protected $loader;

	function __construct(Application $app){		
		$this->loader = new DotEnvLoader('.env');
	}

	function listar() {
		$varenvs = [];
		foreach(Model::get() as $Env) {
			$VarEnv = VarEnvBuilder::build($Env);
			if(!$VarEnv->tipo && $VarEnv->tipo == ''){			
				$VarEnv->valor = $this->loader->normaliseVariable($VarEnv->codigo,$VarEnv->valor);
			}
			array_push($varenvs, $VarEnv);			
		}
		return $varenvs;
	}

	function actualizar($codigo,$valor) {
		$Env = Model::where('codigo',$codigo)->first();
		if($Env && !$Env->bloqueado) {
			$Env->valor = $valor;
			$Env->save();
		} else {
			throw new Exception("Variable Env Bloqueada");			
		}
	}

	function crear($codigo,$valor,$bloqueado = false) {
		$Env = Model::where('codigo',$codigo)->first();
		if(!$Env) {
			$Env2 = new Model();
			$Env2->codigo = $codigo;
			$Env2->valor = $valor;
			$Env2->bloqueado = $bloqueado;
			$Env2->save();
		} else {
			throw new Exception("Variable Env Existente");			
		}
	}

	function borrar($codigo) {
		$Env = Model::where('codigo',$codigo)->first();
		if($Env && !$Env->bloqueado) {		
			$Env->delete();
		} else {
			throw new Exception("Variable Env Bloqueada");			
		}
	}

	function crearEnv(Model $Env) {
		$Env1 = Model::where('codigo',$Env->codigo)->first();
		if(!$Env1) {
			$Env->save();
			return $Env;
		} else {
			throw new Exception("Variable Env Existente");			
		}
	}

	function actualizarEnv(Model $Env) {
		$Env1 = Model::where('codigo',$Env->codigo)->first();
		if($Env1 && !$Env1->bloqueado) {
			$Env1->tipo = $Env->tipo;
			$Env1->valor = $Env->valor;
			$Env1->comentario = $Env->comentario;
			$Env1->save();
			return $Env;
		} else {
			throw new Exception("Variable Env Bloqueada");			
		}
	}

	function existe($codigo) {
		return Model::where('codigo',$codigo)->first();
	}
}	