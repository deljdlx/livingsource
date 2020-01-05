<?php

namespace ElBiniou\LivingSource;

echo 'prototype';
exit();

class GitListener
{

    private $folder;
    private $interval = 2;

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

