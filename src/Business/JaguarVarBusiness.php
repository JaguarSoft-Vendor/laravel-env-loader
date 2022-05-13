<?php 
namespace JaguarSoft\LaravelEnvLoader\Business;

use JaguarSoft\LaravelEnvLoader\Business\VarEnvBusiness;
use JaguarSoft\LaravelEnvLoader\Contract\VarEnvService;

class JaguarVarBusiness {
	protected $business;

	function __construct(VarEnvService $service) {
		$this->business = new VarEnvBusiness($service);
	}

	function env($key, $default = null, $val = null) {		
		$keys = explode(".", $key);
		if(count($keys) > 1) {
			$pkey = array_shift($keys);			
			if(!$this->business->hasOrEnv($pkey)) return $default;			
			return array_get($this->business->getOrEnv($pkey), implode('.', $keys), $default);
		} else {
			return $this->business->getOrEnv($key, $default);
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

	protected function ifElse($key, $comp, callable $compare = null, callable $if = null, callable $else = null) {
		$resp = call_user_func($compare, $key, $comp);
		if ($resp === true) {
			if(!is_null($if)) call_user_func($if, $key, $comp, $this->env($key));				
		} else {
			if(!is_null($else)) call_user_func($else, $key, $comp, $this->env($key));
		}
		return $resp;
	}

	public function ifEquals($key, $comp, callable $callback = null, callable $else = null) {
		return $this->ifElse($key, $comp, function() use ($key, $comp) {
			return $this->env($key) === $comp;
		}, $callback, $else);
	}

	public function ifInArray($key, $arr, callable $callback = null, callable $else = null) {
		if(!is_array($arr)) throw new \Exception('Array is Required');		
		return $this->ifElse($key, $arr, function() use ($key, $arr) {
			return in_array($this->env($key), $arr);
		}, $callback, $else);
	}
   
}	