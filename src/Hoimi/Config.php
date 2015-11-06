<?php
namespace Hoimi;
/**
 * Class Config
 * @package Hoimi
 */
class Config extends ArrayContainer
{
    public function __construct ($caller = null)
    {
        if ($caller !== null && is_file($caller)) {
            $this->loadDirectory(dirname($caller), array(basename($caller)));
        }
    }

    public function loadDirectory ($path, array $exclude)
    {
        $handle = opendir($path);
        while ($fn = readdir($handle)) {
            if ($fn === '.' || $fn === '..' || substr($fn, -4) !== '.php' || in_array($fn, $exclude)) continue;
            $info = pathinfo($fn);
            try {
                $this->loadConfig($info['filename'], "$path/$fn");
            } catch (\Exception $e) {
                closedir($handle);
                throw $e;
            }
        }
        closedir($handle);
    }

    public function loadConfig ($key, $file)
    {
        if (isset($this->array[$key])) {
            throw new \InvalidArgumentException("$key is already exists.");
        }
        $this->array[$key] = require $file;
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return array
     */
    public function addConfig($key, $value)
    {
        $this->array[$key] = $value;
        return $this;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->array = $config;
        return $this;
    }

    public function getConfig()
    {
        return $this->array;
    }
}
