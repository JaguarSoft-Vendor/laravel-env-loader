<?php 
namespace JaguarSoft\LaravelEnvLoader\Contract;
use Illuminate\Contracts\Foundation\Application;

interface VarEnvService {
	function __construct(Application $app);
	function listar();
	function actualizar($codigo,$valor);
	function crear($codigo,$valor,$bloqueado = false);
	function borrar($codigo);  
}	