<?php

namespace ElBiniou\LivingSource;


class Reader
{

    private $metadata;
    private $versionnedSource;


    private $versions = [];

    public function __construct($versionned)
    {

        $this->versionnedSource = $versionned;
        if (is_file($this->versionnedSource)) {
            $this->loadVersionnedSource($this->versionnedSource);
        }
    }

    public function loadVersionnedSource($file)
    {
        $content = file_get_contents($file);

        $data = json_decode($content, true);

        $this->metadata = $data['metadata'];

        if (is_array($data['versions'])) {

            foreach ($data['versions'] as $microtime => $version) {
                $this->versions[$microtime] = $version;
            }
        }
        return $this;
    }


    public function getVersions()
    {
        return $this->versions;
    }

    public function getVersionsIndexes()
    {
        return array_keys($this->versions);
    }

    public function getVersionByIndex($versionId)
    {
        $timestamps = array_keys($this->versions);
        $timestamp = $timestamps[$versionId];
        return $this->versions[$timestamp];
    }

    public function getMetadata()
    {
        return $this->metadata;
    }


}
