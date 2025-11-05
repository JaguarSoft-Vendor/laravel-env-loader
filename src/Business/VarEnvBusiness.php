<?php 
namespace JaguarSoft\LaravelEnvLoader\Business;

use JaguarSoft\LaravelEnvLoader\DotEnvLoader;
use JaguarSoft\LaravelEnvLoader\Contract\VarEnvService;
use JaguarSoft\LaravelEnvLoader\Model\VarEnv;

//use Dotenv\Environment\DotenvFactory;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\Adapter\EnvConstAdapter;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\Adapter\ServerConstAdapter;

class VarEnvBusiness {
	protected $Service;	
	protected $VarEnvs = [];
	protected $varenv_arr = [];	
	protected $inmutable = false;
	protected $repository;

	function __construct(VarEnvService $Service, $inmutable = false, $runInConsole = false){
		$this->Service = $Service;
		$this->inmutable = $inmutable;
		if(!app()->runningInConsole() || $runInConsole === true) {
			$this->VarEnvs = $this->Service->listar();
		}
		$this->varenv_arr = collect($this->VarEnvs)->mapWithKeys(function($VarEnv){
			return [$VarEnv->codigo => $VarEnv->val()];
		});
		$path = app()->environmentPath();
        $file = app()->environmentFile();
        if (!is_string($file)) $file = '.env';    
        $filePath = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$file;
        $adapters = [new EnvConstAdapter, new ServerConstAdapter, new PutenvAdapter];
        $this->repository = RepositoryBuilder::create()
            ->withReaders($adapters)
            ->withWriters($adapters);            
        if($inmutable) {
        	$this->repository = $this->repository->inmutable();
        }
        $this->repository = $this->repository->make();              
	}

	public function merge(VarEnvService $Service) {
		$VarEnvs = $Service->listar();
		foreach($VarEnvs as $VarEnv) {
			$codigo = $VarEnv->codigo;
			$val = $VarEnv->val();
			if(!$this->inmutable || !isset($this->varenv_arr[$codigo])) {
				array_push($this->VarEnvs, $VarEnv);
				$this->varenv_arr[$codigo] = $val;
				if(!is_array($val)) $this->repository->set($codigo, $val);
			}
		}		
		return $this;		
	}

	public function setEnvs() {        
		foreach($this->VarEnvs as $VarEnv) {
			$codigo = $VarEnv->codigo;			
			$val = $VarEnv->val();
			$this->varenv_arr[$codigo] = $val;
			if(!is_array($val)) $this->repository->set($codigo, $val);
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
				//(isset($_ENV[$codigo]) ? $this->handleEnv($_ENV[$codigo]) : env($codigo,$default));
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