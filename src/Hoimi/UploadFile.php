<?php
/**
 * Date: 15/10/01
 * Time: 10:41
 */

namespace Hoimi;


class UploadFile {
    private $path          = null;
    private $tempDirectory = '/tmp';
    private $originalName  = null;
    private $type          = null;

    public function __construct($path = null, $originalName = null, $type = null)
    {
        $this->originalName = $originalName;
        $this->type = $type;
        if (is_null($path)) {
            $this->path = $this->tempDirectory . '/' . uniqid();
        } elseif ($data = @file_get_contents($path)) {
            $this->path = $this->tempDirectory . '/' . uniqid();
            file_put_contents($this->path, $data);
        } else {
            throw new \RuntimeException ('copy failed. ' . $path);
        }
    }

    public function getPath()
    {
        return $this->path;
    }

    public function saveTo($path, $force = false)
    {
        if (!$force && is_file($path))
            throw new \RuntimeException ('"' . $path . '" is already exists.');
        copy($this->path, $path);
        chmod($path, 0777);
    }

    public function saveWithOriginalName($path, $force = false)
    {
        if (!$this->originalName) {
            throw new \RuntimeException('originalName is not set');
        }
        if (is_dir($path)) {
            $path .= strtr($this->originalName, DIRECTORY_SEPARATOR, '-');
            $this->saveTo($path, $force);
        } else {
            throw new \InvalidArgumentException('"' . $path . '" is not directory.');
        }
    }
    public function delete()
    {
        try {
            if (is_dir($this->path)) {
                $this->removeDir($this->path);
            } elseif (is_file($this->path)) {
                @unlink($this->path);
            }
        } catch (\Exception $e){}
    }

    public function __destruct()
    {
        $this->delete();
    }

    /**
     * remove directory recursive
     * @param string $dir path
     */
    public function removeDir($dir) {

        $cnt = 0;
        $handle = opendir($dir);
        if (!$handle) {
            return ;
        }
        while ($item = readdir($handle)) {
            if ($item === "." || $item === "..") {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $cnt = $cnt + $this->removeDir($path);
            }
            else {
                unlink($path);
            }
        }
        closedir($handle);
        if (!rmdir($dir)) {
            return ;
        }
    }

    /**
     * @return string
     */
    public function getTempDirectory()
    {
        return $this->tempDirectory;
    }

    /**
     * @return null
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    public function getType()
    {
        return $this->type;
    }
}