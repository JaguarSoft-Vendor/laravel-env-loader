<?php
namespace JaguarSoft\LaravelEnvLoader;

use Illuminate\Support\Str;
use Dotenv\Loader;
use Dotenv\Lines;
use Dotenv\Parser;
use Dotenv\Environment\DotenvFactory;
use Dotenv\Environment\Adapter\ApacheAdapter;
use Dotenv\Environment\Adapter\EnvConstAdapter;
use Dotenv\Environment\Adapter\ServerConstAdapter;
use PhpOption\Option;

class DotEnvLoader extends Loader {
    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        $this->filePaths = [$filePath];
        $this->envFactory = new DotenvFactory(
            [
                new ApacheAdapter(), 
                new EnvConstAdapter(), 
                new ServerConstAdapter()
            ]);
        $this->setImmutable(false);
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

    // Devuelve array con el numero de linea de cada env
    public function readLines()
    {
        $this->ensureFileIsReadable();        
        $filePath = $this->filePath;
        //$lines = $this->readLinesFromFile($filePath);
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

    /**
     * Attempt to read the files in order.
     *
     * @param string[] $filePaths
     *
     * @throws \Dotenv\Exception\InvalidPathException
     *
     * @return string[]
     */
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

    /**
     * Read the given file.
     *
     * @param string $filePath
     *
     * @return \PhpOption\Option
     */
    protected static function readFromFile($filePath)
    {
        $content = @file_get_contents($filePath);

        return Option::fromValue($content, false);
    }

    /**
     * Determine if the line in the file is a comment or whitespace.
     *
     * @param string $line
     *
     * @return bool
     */
    protected static function isCommentOrWhitespace($line)
    {
        if (trim($line) === '') {
            return true;
        }

        $line = ltrim($line);

        return isset($line[0]) && $line[0] === '#';
    }

}