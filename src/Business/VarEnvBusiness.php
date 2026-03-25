<?php 
namespace JaguarSoft\LaravelEnvLoader\Business;

use JaguarSoft\LaravelEnvLoader\DotEnvLoader;
use JaguarSoft\LaravelEnvLoader\Contract\VarEnvService;
use JaguarSoft\LaravelEnvLoader\Model\VarEnv;

use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Store\StoreBuilder;
use Dotenv\Parser\Parser;

class VarEnvBusiness {
	protected $Service;	
	protected $VarEnvs = [];
	protected $varenv_arr = [];	
	protected $inmutable = false;
	protected $repository;

	function __construct(VarEnvService $Service, $inmutable = false, $runInConsole = false){
		$this->Service = $Service;
		$this->VarEnvs = $this->Service->listar();
		$this->inmutable = $inmutable;				
		$appRunningInConsole = app()->runningInConsole();
    	if(is_null($runInConsole)) $runInConsole = env('VARENV_CONSOLE', true);
		if(!$appRunningInConsole || $runInConsole === true) {
			$this->VarEnvs = $this->Service->listar();
		}
		$this->varenv_arr = collect($this->VarEnvs)->mapWithKeys(function($VarEnv){
			return [$VarEnv->codigo => $VarEnv->val()];
		});
		$this->repository = RepositoryBuilder::createWithDefaultAdapters()->immutable()->make();
	}

	public function merge(VarEnvService $Service) {
		$VarEnvs = $Service->listar();
		foreach($VarEnvs as $VarEnv) {
			$codigo = $VarEnv->codigo;
			$val = $VarEnv->val();
			if(!$this->inmutable || !isset($this->varenv_arr[$codigo])) {
				array_push($this->VarEnvs, $VarEnv);
				$this->varenv_arr[$codigo] = $val;
				if(is_string($val)) $this->repository->set($codigo, $val);
			}
		}		
		return $this;		
	}

	public function setEnvs() {		
		foreach($this->VarEnvs as $VarEnv) {
			if($this->repository->has($VarEnv->codigo)) continue; // No sobreescribe variable .env
			if(is_string($VarEnv->val())) $this->repository->set($VarEnv->codigo, $VarEnv->val());
		}
	}	

	function all($value = false) {
		return array_map(function($Var) use ($value) {			
			return $value === true ? $Var->val() : $Var;			
		},$this->VarEnvs);
	}

	function has($codigo) : bool {		
		return isset($this->varenv_arr[$codigo]);		
	} 

	function hasOrEnv($codigo) : bool {				
		return 	$this->has($codigo) || isset($_ENV[$codigo]);
	}

	function get($codigo, $default = null) {
		return $this->varenv_arr[$codigo] ?? $default;		
	}

	function getOrEnv($codigo, $default = null) {		
		return 	$this->has($codigo) ? $this->get($codigo) : 
				($this->repository->get($codigo) ?? env($codigo,$default));				
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