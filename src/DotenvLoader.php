<?php
namespace JaguarSoft\LaravelEnvLoader;

use Dotenv\Loader;
use Illuminate\Support\Str;

class DotenvLoader extends Loader {

	public function normaliseVariable($name, $value = null)
    {
        list($name, $value) = $this->normaliseEnvironmentVariable($name, $value);
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
        if (strlen($value) > 1 && Str::startsWith($value, '"') && Str::endsWith($value, '"')) {
            return substr($value, 1, -1);
        }
        return $value;
    }

    public function readVariables()
    {        
        $this->ensureFileIsReadable();
        $_envs = [];
        $filePath = $this->filePath;
        $lines = $this->readLinesFromFile($filePath);
        foreach ($lines as $line) {
            if (!$this->isComment($line) && $this->looksLikeSetter($line)) {                
                list($name, $value) = $this->normaliseEnvironmentVariable($line, null);
                $_envs[$name] = $value;
            }
        }
        return $_envs;
    }

    public function setEnvironmentVariable($name, $value = null)
    {
        // If PHP is running as an Apache module and an existing
        // Apache environment variable exists, overwrite it
        if(is_bool($value)) { $value = $value ? 'true' : 'false'; }        
        if (function_exists('apache_getenv') && function_exists('apache_setenv') && apache_getenv($name) !== false) {
            apache_setenv($name, $value);
        }

        if (function_exists('putenv') && is_string($value)) {
            putenv("$name=$value");
        }

        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}