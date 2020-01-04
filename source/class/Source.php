<?php

namespace ElBiniou\LivingSource;


class Source
{
    private $source;
    private $versionnedSource;


    private $versions = [];

    public function __construct($source, $versionned = null)
    {
        $this->source = $source;

        if ($versionned) {
            $this->versionnedSource = $versionned;
            if(is_file($this->versionnedSource)) {
                $this->loadVersionnedSource($this->versionnedSource);
            }

        }
    }

    public function createVersion($name = null, $autoAppend = true, $controlChecksum = true)
    {

        if($name === null) {
            $name = 'version:'.count($this->versions);
        }

        $content = file_get_contents($this->source);

        $version = [
            'id' => uniqid(true),
            'datetime' => date('Y-m-d H:i:s'),
            'name' => $name,
            'content' => $content,
            'checksum' => md5($content).'-'.sha1($content),
        ];

        if($autoAppend) {

            if($controlChecksum) {
                $checksumControl = true;
                foreach ($this->versions as $registeredVersion) {

                    if($registeredVersion['checksum'] == $version['checksum']) {
                        $checksumControl = false;
                        break;
                    }
                }
            }
            if(!$controlChecksum || $checksumControl) {
                $this->addVersion($version);
            }

        }
        return $version;
    }



    public function addVersion($version)
    {
        $key = microtime(true);
        $this->versions[$key] = $version;
        return $this;
    }


    public function loadVersionnedSource($file)
    {
        $content = file_get_contents($file);

        $data = json_decode($content, true);

        if(is_array($data)) {

            foreach ($data['versions'] as $microtime => $version) {
                $this->versions[$microtime] = $version;
            }
        }
        return $this;
    }

    public function save($output = null)
    {
        if($output) {
            $this->versionnedSource = $output;
            $this->loadVersionnedSource($this->versionnedSource);
        }

        if(empty($this->versions)) {
            $this->createVersion();
        }

        ksort($this->versions);

        file_put_contents(
            $this->versionnedSource,
            json_encode(
                [
                    'metadata' => [
                        'file' => realpath($this->source),
                        'mime' => mime_content_type($this->source),
                    ],
                    'versions' => $this->versions,
                ],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            )
        );

        return $this;



    }


}