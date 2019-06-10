<?php

namespace j0hnys\Trident\Base\Storage;

class Disk
{   
    /**
     * @var string
     */
    private $base_path;

    public function __construct()
    {
        $this->base_path = base_path();   
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->base_path;
    }

    /**
     * @param string $path
     * @param array $options
     * @return string
     */
    public function readFile(string $path, array $options = []): string
    {
        return \file_get_contents($path);
    }

    /**
     * @param string $path
     * @param array $options
     * @return array
     */
    public function readFileArray(string $path, array $options = []): array
    {
        return file($path);
    }

    /**
     * @param string $path
     * @param string $content
     * @param array $options
     * @return int|bool
     */
    public function writeFile(string $path, string $content, array $options = [])
    {
        return file_put_contents($path,$content);
    }

    /**
     * @param string $path
     * @return boolean
     */
    public function fileExists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * @param string $path
     * @return array
     */
    public function getFolderFileNames(string $path): array
    {
        $files = scandir($path);

        $filenames = [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && !is_dir($path.$file)) {
                $filenames []= $file;
            }
        }

        return $filenames;
    }

    /**
     * @param string $path
     * @return void
     */
    public function makeDirectory(string $path): void
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
    }

    /**
     * @param string $dir
     * @return bool
     */
    public function deleteDirectoryAndFiles(string $dir): bool {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? $this->deleteDirectory("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir);
        }
    }

    /**
     * @param string $source
     * @param string $destination
     * @return void
     */
    public function copyFolderStructure(string $source, string $destination): void
    {
        mkdir($destination, 0755);
        foreach (
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                //i don't want to copy any file now
                // copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

    /**
     * @param string $source
     * @param string $destination
     * @return void
     */
    public function copyFiles(string $source, string $destination): void
    {
        foreach (
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                //i don't want to copy any folder
                // mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                //i don't want to copy any file now
                copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

    /**
     * @param string $source
     * @param string $destination
     * @return void
     */
    public function copyFoldersAndFiles(string $source, string $destination): void
    {
        foreach (
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                //i don't want to copy any folder
                mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                //i don't want to copy any file now
                copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

    /**
     * @param string $absolute_folder_path
     * @return array
     */
    public function getFolderFiles(string $absolute_folder_path): array
    {
        $files = scandir($absolute_folder_path);

        $filenames = [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filenames []= str_replace('.php','',$file);
            }
        }

        return $filenames;
    }

}