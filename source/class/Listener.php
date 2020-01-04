<?php

namespace ElBiniou\LivingSource;

class Listener
{

    const EVENT_NEW = 2;
    const EVENT_UPDATED = 4;

    private $folder;
    private $interval = 2;

    private $filters = [];


    private $changeString = 'Changes not staged for commit:';
    private $changedItemString = 'modified:   (.*?$)';

    private $callbacks;


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

        $metadatas = [];


        foreach ($files as $file) {
            clearstatcache();
            if (is_file($file)) {
                $checksum = md5(file_get_contents($file));

                $metadatas[$file] = array(
                    'modified' => filemtime($file),
                    'checksum' => $checksum
                );
            }
        }


        while (true) {

            clearstatcache();
            foreach ($files as $file) {
                if (!is_file($file)) {
                    continue;
                }

                $modified = filemtime($file);
                if (!isset($metadatas[$file])) {
                    foreach ($this->callbacks as $callback) {
                        call_user_func_array($callback, [$file, self::EVENT_NEW]);
                        //$callback($file, self::EVENT_NEW);
                    }
                }
                else {

                    if ($modified !== $metadatas[$file]['modified']) {

                        $checksum = md5(file_get_contents($file));
                        if ($checksum != $metadatas[$file]['checksum']) {
                            foreach ($this->callbacks as $callback) {
                                $callback($file, self::EVENT_UPDATED);
                            }

                            $metadatas[$file]['checksum'] = $checksum;
                        }
                        $metadatas[$file]['modified'] = $modified;
                    }
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
                $results[] = $path;
            }
            else if ($value != "." && $value != "..") {
                $this->getDirContents($path, $results);
                $results[] = $path;
            }
        }

        return $results;
    }


    public function gitListen()
    {
        chdir($this->folder);
        while (true) {
            exec('git status', $output);

            $output = implode("\n", $output);


            if (preg_match('`' . $this->changeString . '`', $output)) {
                echo "\n";
                preg_match_all('`' . $this->changedItemString . '`m', $output, $matches);

                if (!empty($matches[1])) {
                    foreach ($matches[1] as $file) {

                        echo 'handling ' . trim($file) . "\n";

                        foreach ($this->callbacks as $callback) {
                            $callback($this->folder . '/' . $file);
                        }

                    }
                }
            }
            else {
                echo "\r" . date('H:m:s') . "\tlistening";
            }

            sleep($this->interval);
        }

    }


}

