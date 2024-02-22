<?php 
namespace JaguarSoft\LaravelEnvLoader\Business;

use JaguarSoft\LaravelEnvLoader\Contract\VarEnvService;
use JaguarSoft\LaravelEnvLoader\Model\VarEnv;

use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryBuilder;
use PhpOption\Option;

class VarEnvBusiness {
	protected $Service;	
	protected $VarEnvs = [];

	function __construct(VarEnvService $Service){
		$this->Service = $Service;
		$this->VarEnvs = $this->Service->listar();		
	}

	public function setEnvs() {
		$builder = RepositoryBuilder::createWithDefaultAdapters();
		//$builder = $builder->addAdapter(PutenvAdapter::class);            
		$repository = $builder->immutable()->make();

		foreach($this->VarEnvs as $VarEnv) {
			if($repository->has($VarEnv->codigo)) continue; // No sobreescribe variable .env
			$repository->set($VarEnv->codigo, $VarEnv->val());
		}
	}

	function all($value = false) {
		return array_map(function($Var) use ($value) {			
			return $value === true ? $Var->val() : $Var;			
		},$this->VarEnvs);
	}

	function has($codigo) : bool {		
		foreach($this->VarEnvs as $VarEnv) {
			if($VarEnv->codigo === $codigo) {
				return true;
			}
		};
		return false;
	} 

	function hasOrEnv($codigo) : bool {				
		return 	$this->has($codigo) || isset($_ENV[$codigo]);
	}

	function get($codigo, $default = null) {
		foreach($this->VarEnvs as $VarEnv) {
			if($VarEnv->codigo === $codigo) {
				return $VarEnv->val();
			}
		};
		return $default;
	}

	function getOrEnv($codigo, $default = null) {
		return 	$this->has($codigo) ? $this->get($codigo) : 
				(isset($_ENV[$codigo]) ? DotEnvLoader::env($_ENV[$codigo]) : env($codigo,$default));
	}

	function post($codigo, $valor) {
		if(empty($valor)) $valor = "''";
		$this->Service->crear($codigo, $valor);
	}

	function put($codigo, $valor) {
		if(empty($valor)) $valor = "''";
		$this->Service->actualizar($codigo, $valor);
	}

	function delete($codigo, $valor) {
		$this->Service->borrar($codigo, $valor);
	}
}	