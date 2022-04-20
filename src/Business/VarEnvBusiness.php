<?php 
namespace JaguarSoft\LaravelEnvLoader\Business;

use JaguarSoft\LaravelEnvLoader\DotenvLoader;
use JaguarSoft\LaravelEnvLoader\Contract\VarEnvService;
use JaguarSoft\LaravelEnvLoader\Model\VarEnv;


class VarEnvBusiness {
	protected $Service;	
	protected $VarEnvs = [];

	function __construct(VarEnvService $Service){
		$this->Service = $Service;
		$this->VarEnvs = $this->Service->listar();
		//$this->setEnvs();
	}

	public function setEnvs() {
		$path = app()->environmentPath();
        $file = app()->environmentFile();
        if (!is_string($file)) $file = '.env';    
        $filePath = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$file;        
        $loader = new DotenvLoader($filePath, true);
        $envs = $loader->readVariables();
		//dd($this->VarEnvs);
		foreach($this->VarEnvs as $VarEnv) {
			if(isset($envs[$VarEnv->codigo])) continue;
			$loader->setEnvironmentVariable($VarEnv->codigo, $VarEnv->valor);
		}
	}

	function all() {
		return array_map(function($Var){
			return $Var;
			return $Var->val();
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

	function get($codigo, $default = null) : ?VarEnv {		
		foreach($this->VarEnvs as $VarEnv) {
			if($VarEnv->codigo === $codigo) {
				return $VarEnv->val();
			}
		};
		return VarEnv::from($codigo, env($codigo, $default));
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