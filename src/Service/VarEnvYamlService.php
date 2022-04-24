<?php 
namespace JaguarSoft\LaravelEnvLoader\Service;

use JaguarSoft\LaravelEnvLoader\Model\VarEnv;
use JaguarSoft\LaravelEnvLoader\Contract\VarEnvService;
use JaguarSoft\LaravelEnvLoader\DotenvLoader;

use Illuminate\Contracts\Foundation\Application;
use Exception;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class VarEnvYamlService implements VarEnvService {
	protected $app;

	function __construct(Application $app){
		$this->app = $app;
	}

	function listar() {
		$varenvs = [];
		$envFile = $this->app->environmentFile();
		$path = env('VARENV_DIR','');		
		$path = base_path($path).($path ? DIRECTORY_SEPARATOR.$envFile.'.yml' : $envFile.'.yml');
		if(!file_exists($path)) return [];
		$envs = [];
        try {
            $envs = Yaml::parse(file_get_contents($path), Yaml::PARSE_CONSTANT);        
        } catch (ParseException $e) {
            //dd($e->getMessage());
        }
		foreach($envs as $name => $value) {
			array_push($varenvs, VarEnv::from($name,$value));
		}		
		return $varenvs;
	}

	function actualizar($codigo,$valor) {
		throw new Exception("VarEnvYamlService.actualizar No Implementado");
	}

	function crear($codigo,$valor,$bloqueado = false) {
		throw new Exception("VarEnvYamlService.crear No Implementado");
	}

	function borrar($codigo) {
		throw new Exception("VarEnvYamlService.borrar No Implementado");
	}
}	