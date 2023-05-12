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
			$val = $VarEnv->val();
			if(is_array($val)) {
				$val = json_encode($val);
			}
			$loader->setEnvironmentVariable($VarEnv->codigo, $val);
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
				(isset($_ENV[$codigo]) ? $this->handleEnv($_ENV[$codigo]) : $this->env($codigo,$default));
	}

	function env($codigo, $default) {
		$env = env($codigo, null);
		if(is_null($env)) return $default;
		if(is_string($env)) {
			$json = json_decode($env);
			if(json_last_error() === JSON_ERROR_NONE) return $json;
		}
   		return $env;
	}

	protected function handleEnv($value) {
		if(!is_string($value)) return $value;
		switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
            default:
            	$json = json_decode($value, true);            	
            	if(json_last_error() === JSON_ERROR_NONE) return $json;
            	return $value;
        }
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