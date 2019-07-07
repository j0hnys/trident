<?php

namespace j0hnys\Trident\Base\Storage;

class Trident
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
     * @param string $path
     * @return void
     */
    public function setBasePath(string $path): void
    {
        $this->base_path = $path;
    }

    /**
     * @return array
     */    
    public function getCurrentControllers(): array
    {
        $files = scandir($this->base_path . '/app/Http/Controllers/Trident/');

        $filenames = [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filenames[] = str_replace('Controller.php', '', $file);
            }
        }

        return $filenames;
    }

    /**
     * @return array
     */
    public function getCurrentWorkflows(): array
    {
        $files = scandir($this->base_path.'/app/Trident/Workflows/Logic/');

        $filenames = [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filenames []= str_replace('.php','',$file);
            }
        }

        return $filenames;
    }

    /**
     * @return array
     */
    public function getCurrentBusinesses(): array
    {
        $files = scandir($this->base_path.'/app/Trident/Business/Logic/');

        $filenames = [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filenames []= str_replace('.php','',$file);
            }
        }

        return $filenames;
    }

    /**
     * @param string $type
     * @return array
     */
    public function getCurrentEvents(string $type): array
    {
        $files = scandir($this->base_path.'/app/Trident/'.$type.'/Events/Triggers/');

        $filenames = [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filenames []= str_replace('Trigger.php','',$file);
            }
        }

        return $filenames;
    }

    /**
     * @param string $type
     * @return array
     */
    public function getCurrentSubscribers(string $type): array
    {
        $files = scandir($this->base_path.'/app/Trident/'.$type.'/Events/Subscribers/');

        $filenames = [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filenames []= str_replace('Subscriber.php','',$file);
            }
        }

        return $filenames;
    }

}