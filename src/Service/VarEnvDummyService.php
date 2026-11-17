<?php 
namespace JaguarSoft\LaravelEnvLoader\Service;

use JaguarSoft\LaravelEnvLoader\Model\VarEnv;
use JaguarSoft\LaravelEnvLoader\Contract\VarEnvService;
use JaguarSoft\LaravelEnvLoader\Model\Env;

use Illuminate\Contracts\Foundation\Application;
use Exception;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class VarEnvDummyService implements VarEnvService {
	protected $app;
	protected $runInConsole = true;
	protected $provider = 'dummy';

	function __construct(Application $app){
		$this->app = $app;
	}

	function getProvider() {
		return $this->provider;
	}

	function runInConsole() {
		return $this->runInConsole;
	}

	function listar() {
		return [];
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