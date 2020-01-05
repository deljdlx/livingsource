<?php

namespace ElBiniou\LivingSource;


class SourceFileStorage extends SourceStorage
{

    /**
     * @var Source
     */
    private $versionnedSource;




    public function __construct(Source $source, $versionned)
    {
        parent::__construct($source);

        $this->versionnedSource = $versionned;
        if (is_file($this->versionnedSource)) {
            $this->loadMetadata();
            $this->loadVersions();
        }
    }


    public function loadMetadata()
    {
        if (!is_file($this->versionnedSource)) {
            return $this;
        }

        $content = file_get_contents($this->versionnedSource);
        $data = json_decode($content, true);
        $this->source->loadMetadata($data['metadata']);
    }

    public function getVersion()
    {
        $versions =$this->source->getVersions();
        foreach ($versions as $version) {
            yield $version;
        }
    }


    public function loadVersions()
    {

        if (!is_file($this->versionnedSource)) {
            return $this;
        }

        $content = file_get_contents($this->versionnedSource);
        $data = json_decode($content, true);

        if (is_array($data)) {
            foreach ($data['versions'] as $microtime => $version) {
                $this->source->addVersion($version, $microtime);
            }
        }

        return $this;
    }


    public function save()
    {

        $this->loadVersions();

        $this->doBeforeSave();

        $versions = $this->source->getVersions();


        file_put_contents(
            $this->versionnedSource,
            json_encode(
                [
                    'metadata' => $this->source->getMetadata(),
                    'versions' => $versions,
                ],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            )
        );

        return $this;


    }


}