<?php
namespace JaguarSoft\LaravelEnvLoader\Model;

use Illuminate\Database\Eloquent\Model;

class Env extends Model
{
    protected $table = 'tb_envs';
    protected $primaryKey = 'id_env';

    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = 
        [
            'tipo'
            ,'codigo'
            ,'valor'
            ,'codigo_padre'
            ,'bloqueado'
            ,'comentario'
        ];
    protected $visible  = 
        [
            'id_env'
            ,'tipo'
            ,'codigo'
            ,'valor'
            ,'codigo_padre'
            ,'bloqueado'
            ,'comentario'
        ];

    protected $informative = ['codigo'];
    protected $name = 'Variable de Entorno';
}
