<?php 
namespace JaguarSoft\LaravelEnvLoader\Service;

use JaguarSoft\LaravelEnvLoader\Model\VarEnv;
use JaguarSoft\LaravelEnvLoader\Contract\VarEnvService;
use JaguarSoft\LaravelEnvLoader\Model\Env;

use Illuminate\Contracts\Foundation\Application;
use Exception;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class VarEnvYamlService implements VarEnvService {
	protected $app;
	protected $runInConsole = true;
	protected $provider = 'yaml';

	function __construct(Application $app){
		$this->app = $app;
		$this->runInConsole = env('ENV_YAML_IN_CONSOLE', true);
	}

	function getProvider() {
		return $this->provider;
	}

	function runInConsole() {
		return $this->runInConsole;
	}

	function listar() {
		$varenvs = [];
		$envFile = $this->app->environmentFile();
		$path = env('VARENV_DIR','');
		$path = base_path($path).(!empty($path) ? DIRECTORY_SEPARATOR : '').$envFile.'.yml';
		if(!file_exists($path)) return [];
		$envs = [];
        try {
            $envs = Yaml::parse(file_get_contents($path), Yaml::PARSE_CONSTANT) ?? [];        
        } catch (ParseException $e) {
        }
		foreach($envs as $name => $value) {
			$VarEnv = VarEnv::from($name,$value);
			$VarEnv->provider = $this->provider;
			array_push($varenvs, $VarEnv);
		}		
		return $varenvs;
	}

	function actualizar($codigo, $valor) {
		throw new Exception("VarEnvYamlService.actualizar No Implementado");
	}

	function crear($codigo, $valor, $bloqueado = false) {
		throw new Exception("VarEnvYamlService.crear No Implementado");
	}

	function borrar($codigo) {
		throw new Exception("VarEnvYamlService.borrar No Implementado");
	}

	function crearEnv(Env $Env) {
		throw new Exception("VarEnvYamlService.crearEnv No Implementado");
	}

	function actualizarEnv(Env $Env) {
		throw new Exception("VarEnvYamlService.actualizarEnv No Implementado");
	}

	function existe($codigo) {
		throw new Exception("VarEnvYamlService.existe No Implementado");	
	}
}	