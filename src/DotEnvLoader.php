<?php
namespace JaguarSoft\LaravelEnvLoader;

use Dotenv\Loader;
use Dotenv\Parser;
use DotEnvParser;
use Illuminate\Support\Str;


class DotEnvLoader extends Loader {

	public function normaliseVariable($name, $value = null)
    {
        list($name, $value) = Parser::parse("$name=$value");            
        return $this->env($value);
    }

    protected function env($value)
    {
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }
        if (preg_match('/\A([\'"])(.*)\1\z/', $value, $matches)) {
            return $matches[2];
        }
        return $value;
    }

    public function getVariables() {
        return $this->envVariables;
    }

    public function getVariable($name)
    {
        return $this->env($this->getEnvironmentVariable($name));
    }
}