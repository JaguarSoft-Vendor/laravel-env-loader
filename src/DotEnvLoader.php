<?php
namespace JaguarSoft\LaravelEnvLoader;

use Illuminate\Support\Str;
use Dotenv\Loader\Loader;
use Dotenv\Loader\Lines;
use Dotenv\Loader\Parser;
use Dotenv\Repository\RepositoryInterface;
use PhpOption\Option;

class DotEnvLoader extends Loader {
    protected $filePath;

    /**
     * Process the environment variable entries.
     *
     * We'll fill out any nested variables, and acually set the variable using
     * the underlying environment variables instance.
     *
     * @param \Dotenv\Repository\RepositoryInterface $repository
     * @param string[]                               $entries
     *
     * @throws \Dotenv\Exception\InvalidFileException
     *
     * @return array<string,string|null>
     */
    protected function getEntries(RepositoryInterface $repository, array $entries)
    {
        $vars = [];

        foreach ($entries as $entry) {
            list($name, $value) = Parser::parse($entry);
            if ($this->whitelist === null || in_array($name, $this->whitelist, true)) {
                $vars[$name] = self::resolveNestedVariables($repository, $value);
                //$repository->set($name, $vars[$name]);
            }
        }

        return $vars;
    }

    public function normaliseVariable($name, $value = null)
    {        
        list($name, $value) = Parser::parse("$name=$value");
        return $this->env($value);
    }

    public static function env($value, $default = null)
    {
        return Option::fromValue($value)
            ->map(function ($value) {
                if(is_string($value)) {
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
                            return null;
                    }
                    /*
                    if (preg_match('/\A([\'"])(.*)\1\z/', $value, $matches)) {
                        return $matches[2];
                    }
                    */
                }                

                return $value;
            })
            ->getOrElse($default);
    }

    public function readVariables()
    {        
        $content = self::findAndRead($this->filePaths);
        $entries = Lines::process(preg_split("/(\r\n|\n|\r)/", $content));

        $vars = [];

        foreach ($entries as $entry) {
            list($name, $value) = Parser::parse($entry);
            $vars[$name] = $this->resolveNestedVariables($value);
        }

        return $vars;
    }

}