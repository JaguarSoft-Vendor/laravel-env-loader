<?php
namespace JaguarSoft\LaravelEnvLoader;

use Illuminate\Support\Str;
use Dotenv\Loader\Resolver;
use Dotenv\Parser\Entry;
use Dotenv\Parser\Parser;
use Dotenv\Parser\Value;
use Dotenv\Repository\RepositoryInterface;
use PhpOption\Option;

class DotEnvLoader {    

    /**
     * Load the given entries into the repository.
     *
     * We'll substitute any nested variables, and send each variable to the
     * repository, with the effect of actually mutating the environment.
     *
     * @param \Dotenv\Repository\RepositoryInterface $repository
     * @param \Dotenv\Parser\Entry[]                 $entries
     *
     * @return array<string, string|null>
     */
    public function read(RepositoryInterface $repository, array $entries)
    {
        /** @var array<string, string|null> */
        return \array_reduce($entries, static function (array $vars, Entry $entry) use ($repository) {
            $name = $entry->getName();

            $value = $entry->getValue()->map(static function (Value $value) use ($repository) {
                return Resolver::resolve($repository, $value);
            });

            if ($value->isDefined()) {
                $inner = $value->get();
                //if ($repository->set($name, $inner)) {
                    return \array_merge($vars, [$name => $inner]);
                //}
            } else {
                //if ($repository->clear($name)) {
                    return \array_merge($vars, [$name => null]);
                //}
            }

            return $vars;
        }, []);
    }    

    public function normaliseVariable($name, $value = null)
    {        
        list($name, $value) = Parser::parse($name.'='.$value);
        return $this->env($value);
    }

    public static function env($value, $default = null)
    {
        return Option::fromValue($value)
            ->map(function ($value) {
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

                return $value;
            })
            ->getOrElse($default);
    }
    /*
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
    */
    // Devuelve array con el numero de linea de cada env
    /*
    public function readLines()
    {
        $this->ensureFileIsReadable();        
        $filePath = $this->filePath;        
        $autodetect = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', '1');
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
        $env_line = [];
        ini_set('auto_detect_line_endings', $autodetect);
        foreach ($lines as $k => $line) {
            if(empty($line)) continue;
            if(self::isCommentOrWhitespace($line)) continue;            
            list($name, $value) = Parser::parse($line);
            //$value = $this->resolveNestedVariables($value);
            $env_line[$name] = $k;
        }

        return $env_line;
    }
    */

    /**
     * Attempt to read the files in order.
     *
     * @param string[] $filePaths
     *
     * @throws \Dotenv\Exception\InvalidPathException
     *
     * @return string[]
     */
    /*
    protected static function findAndRead(array $filePaths)
    {
        if ($filePaths === []) {
            throw new InvalidPathException('At least one environment file path must be provided.');
        }

        foreach ($filePaths as $filePath) {
            $lines = self::readFromFile($filePath);
            if ($lines->isDefined()) {
                return $lines->get();
            }
        }

        throw new InvalidPathException(
            sprintf('Unable to read any of the environment file(s) at [%s].', implode(', ', $filePaths))
        );
    }
    */

    /**
     * Read the given file.
     *
     * @param string $filePath
     *
     * @return \PhpOption\Option
     */
    /*
    protected static function readFromFile($filePath)
    {
        $content = @file_get_contents($filePath);

        return Option::fromValue($content, false);
    }
    */

    /**
     * Determine if the line in the file is a comment or whitespace.
     *
     * @param string $line
     *
     * @return bool
     */
    /*
    protected static function isCommentOrWhitespace($line)
    {
        if (trim($line) === '') {
            return true;
        }

        $line = ltrim($line);

        return isset($line[0]) && $line[0] === '#';
    }
    */
}