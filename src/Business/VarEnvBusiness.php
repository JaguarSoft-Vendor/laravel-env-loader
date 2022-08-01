<?php 
namespace JaguarSoft\LaravelEnvLoader\Business;

use JaguarSoft\LaravelEnvLoader\DotEnvLoader;
use JaguarSoft\LaravelEnvLoader\Contract\VarEnvService;
use JaguarSoft\LaravelEnvLoader\Model\VarEnv;


class VarEnvBusiness {
	protected $Service;	
	protected $VarEnvs = [];

	function __construct(VarEnvService $Service){
		$this->Service = $Service;
		$this->VarEnvs = $this->Service->listar();		
	}

	public function setEnvs() {
		$path = app()->environmentPath();
        $file = app()->environmentFile();
        if (!is_string($file)) $file = '.env';    
        $filePath = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$file;        
        $loader = new DotEnvLoader($filePath, true);
        $envs = $loader->readVariables();
		foreach($this->VarEnvs as $VarEnv) {
			if(isset($envs[$VarEnv->codigo])) continue;
			$loader->setEnvironmentVariable($VarEnv->codigo, $VarEnv->val());
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
				(isset($_ENV[$codigo]) ? $_ENV[$codigo] : env($codigo,$default));
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