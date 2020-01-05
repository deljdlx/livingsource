<?php

namespace ElBiniou\LivingSource;

class Listener
{

    const EVENT_NEW = 2;
    const EVENT_UPDATED = 4;
    const EVENT_DELETE = 8;

    private $folder;
    private $interval = 1;

    private $filters = [];

    private $callbacks;

    private $fileData = [];


    public function __construct($folder)
    {
        $this->folder = $folder;
    }


    public function register(Callable $callback)
    {
        $this->callbacks[] = $callback;
        return $this;
    }

    public function listen()
    {
        $files = $this->getDirContents($this->folder);

        foreach ($files as $file) {
            clearstatcache();
            if (is_file($file)) {
                $checksum = md5(file_get_contents($file));

                $this->fileData[$file] = array(
                    'modified' => filemtime($file),
                    'checksum' => $checksum
                );
            }
        }


        while (true) {

            clearstatcache();

            $currentFiles = $this->getDirContents($this->folder);

            foreach ($currentFiles as $file) {
                if (!isset($this->fileData[$file])) {

                    if (!is_file($file)) {
                        $this->handleNewFolder($file);
                    }
                    else {
                        $this->handleNewFile($file);
                    }
                }
                else {
                    $modified = filemtime($file);

                    if ($modified !== $this->fileData[$file]['modified']) {
                        if(is_file($file)) {
                            $this->handleChangeFile($file);
                        }
                    }
                }
            }

            foreach ($this->fileData as $path => $value) {
                if(!array_key_exists($path, $currentFiles)) {
                    $this->handleDelete($path);
                }
            }


            sleep($this->interval);
        }
    }

    public function getDirContents($dir, &$results = array())
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[$path] = $path;
            }
            else if ($value != "." && $value != "..") {
                $this->getDirContents($path, $results);
                $results[$path] = $path;
            }
        }

        return $results;
    }


    private function handleDelete($file)
    {
        $event = new Event([
            'file' => $file,
            'type' => self::EVENT_DELETE,
            'time' => time()
        ]);
        foreach ($this->callbacks as $callback) {
            call_user_func_array($callback, [$event]);
        }
        unset($this->fileData[$file]);
    }


    private function handleNewFolder($folder)
    {
        $modified = filemtime($folder);
        $checksum = '';
        $this->fileData[$folder]['modified'] = $modified;
        $this->fileData[$folder]['checksum'] = $checksum;
    }


    private function handleNewFile($file)
    {
        $event = new Event([
            'file' => $file,
            'type' => self::EVENT_NEW,
            'time' => time()
        ]);
        foreach ($this->callbacks as $callback) {
            call_user_func_array($callback, [$event]);
        }

        $modified = filemtime($file);
        $checksum = md5(file_get_contents($file));
        $this->fileData[$file]['modified'] = $modified;
        $this->fileData[$file]['checksum'] = $checksum;
    }

    private function handleChangeFile($file)
    {
        $modified = filemtime($file);
        $checksum = md5(file_get_contents($file));
        if ($checksum != $this->fileData[$file]['checksum']) {
            $event = new Event([
                'file' => $file,
                'type' => self::EVENT_UPDATED,
                'time' => time()
            ]);
            foreach ($this->callbacks as $callback) {
                call_user_func_array($callback, [$event]);
            }
            $this->fileData[$file]['checksum'] = $checksum;
        }
        $this->fileData[$file]['modified'] = $modified;
    }



}

