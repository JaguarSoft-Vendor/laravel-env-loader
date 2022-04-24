<?php 
namespace JaguarSoft\LaravelEnvLoader\Business;

use JaguarSoft\LaravelEnvLoader\Business\VarEnvBusiness;
use JaguarSoft\LaravelEnvLoader\Contract\VarEnvService;

class JaguarVarBusiness {
	protected $business;

	function __construct(VarEnvService $service) {
		$this->business = new VarEnvBusiness($service);
	}

	function env($codigo, $default = null, $valor = null) {		
		$keys = explode(".", $codigo);
		if(count($keys) > 1) {
			$pkey = array_shift($keys);			
			if(!$this->business->hasOrEnv($pkey)) return $default;			
			return array_get($this->business->getOrEnv($pkey), implode('.', $keys), $default);
		} else {
			return $this->business->getOrEnv($codigo, $default);
		}
	}

	function all() {
		$envs = [];
		foreach($this->business->all() as $VarEnv) {
			$envs[$VarEnv->codigo] = $VarEnv->val();
			//$envs[$VarEnv->codigo] = $VarEnv;
		}
		return $envs;
	}
   
}	