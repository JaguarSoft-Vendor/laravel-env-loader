<?php
namespace JaguarSoft\LaravelEnvLoader;

use Illuminate\Support\Str;
use Dotenv\Loader\Loader;
use Dotenv\Loader\Lines;
use Dotenv\Loader\Parser;
use Dotenv\Loader\Value;
use Dotenv\Regex\Regex;
use Dotenv\Repository\RepositoryInterface;
use PhpOption\Option;

class DotEnvLoader extends Loader {
    protected $filePath;

     /**
     * Load the given environment file content into the repository.
     *
     * @param \Dotenv\Repository\RepositoryInterface $repository
     * @param string                                 $content
     *
     * @throws \Dotenv\Exception\InvalidFileException
     *
     * @return array<string,string|null>
     */
    public function read(RepositoryInterface $repository, $content)
    {
        return $this->getEntries(
            $repository,
            Lines::process(Regex::split("/(\r\n|\n|\r)/", $content)->getSuccess())
        );
    }

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
        //list($name, $value) = Parser::parse("$name=$value");
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

                    if (preg_match('/\A([\'"])(.*)\1\z/', $value, $matches)) {
                        return $matches[2];
                    }
                }                

                return $value;
            })
            ->getOrElse($default);
    }

    /**
     * Resolve the nested variables.
     *
     * Look for ${varname} patterns in the variable value and replace with an
     * existing environment variable.
     *
     * @param \Dotenv\Repository\RepositoryInterface $repository
     * @param \Dotenv\Loader\Value|null              $value
     *
     * @return string|null
     */
    protected static function resolveNestedVariables(RepositoryInterface $repository, Value $value = null)
    {
        /** @var Option<Value> */
        $option = Option::fromValue($value);

        return $option
            ->map(function (Value $v) use ($repository) {
                /** @var string */
                return array_reduce($v->getVars(), function ($s, $i) use ($repository) {
                    return substr($s, 0, $i).self::resolveNestedVariable($repository, substr($s, $i));
                }, $v->getChars());
            })
            ->getOrElse(null);
    }

    /**
     * Resolve a single nested variable.
     *
     * @param \Dotenv\Repository\RepositoryInterface $repository
     * @param string                                 $str
     *
     * @return string
     */
    protected static function resolveNestedVariable(RepositoryInterface $repository, $str)
    {
        return Regex::replaceCallback(
            '/\A\${([a-zA-Z0-9_.]+)}/',
            function (array $matches) use ($repository) {
                return Option::fromValue($repository->get($matches[1]))
                    ->getOrElse($matches[0]);
            },
            $str,
            1
        )->success()->getOrElse($str);
    }

}