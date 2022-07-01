<?php 
namespace JaguarSoft\LaravelEnvLoader\Contract;
use Illuminate\Contracts\Foundation\Application;
use JaguarSoft\LaravelEnvLoader\Model\Env;

interface VarEnvService {
	function __construct(Application $app);
	function listar();
	function actualizar($codigo,$valor);
	function crear($codigo,$valor,$bloqueado = false);
	function borrar($codigo); 
	function crearEnv(Env $Env);
	function actualizarEnv(Env $Env);
	function existe($codigo);
}	