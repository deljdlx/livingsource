<?php

namespace ElBiniou\LivingSource;


class Source
{
    protected $source;

    /**
     * @var Version[]
     */
    protected $versions = [];

    public function __construct($source = null)
    {
        $this->source = $source;
    }


    public function getPath()
    {
        return $this->source;
    }

    public function getName()
    {
        return basename($this->source);
    }


    public function getMetadata()
    {
        return [
            'file' => realpath($this->getPath()),
            'mime' => $this->getMime(),
        ];
    }

    public function createVersion($name = null, $description = '', $autoAppend = true, $controlChecksum = true)
    {

        if ($name === null) {
            $name = 'version:' . count($this->versions);
        }

        $content = file_get_contents($this->source);

        $data = [
            'id' => uniqid(true),
            'datetime' => date('Y-m-d H:i:s'),
            'name' => $name,
            'description' => $description,
            'content' => $content,
            'checksum' => md5($content) . '-' . sha1($content),
        ];

        $version = new Version();
        $version->loadFromArray($data);



        if ($autoAppend) {

            if ($controlChecksum) {
                $checksumControl = true;
                foreach ($this->versions as $registeredVersion) {

                    if ($registeredVersion->getChecksum() == $version->getChecksum()) {
                        $checksumControl = false;
                        break;
                    }
                }
            }
            if (!$controlChecksum || $checksumControl) {
                $this->addVersion($version);
            }

        }
        return $version;
    }


    public function getVersions()
    {
        ksort($this->versions);
        return $this->versions;
    }

    public function getVersion()
    {
        $versions =$this->getVersions();
        foreach ($versions as $version) {
            yield $version;
        }
    }



    public function addVersion($version, $key = null)
    {
        if(is_array($version)) {
            $versionInstance = new Version();
            $versionInstance->loadFromArray($version);
            $version = $versionInstance;
        }

        if ($key === null) {
            $key = microtime(true);
        }

        $this->versions[$key] = $version;
        return $this;
    }

    public function getMime()
    {
        if($metadata = $this->getMetadata()) {
            return $metadata['mime'];
        }
        else {
            if($this->source) {
                return mime_content_type($this->source);
            }
        }

        return false;

    }


}