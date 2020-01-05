<?php

namespace ElBiniou\LivingSource;


class SourcePathStorage extends SourceStorage
{


    protected $path;


    public function __construct(Source $source, $path, $autoCreate = false)
    {
        if($autoCreate && !is_dir($path)) {
            mkdir($path);
        }
        parent::__construct($source);
        $this->path = $path;

    }


    public function loadVersions()
    {
        $dir = opendir($this->path);
        while($file = readdir($dir)) {
            if($file != '.' && $file !='..') {
                 $version = $this->loadVersionFile($this->path.'/'.$file);
                 if($version) {
                     $this->source->addVersion($version['version'], strtotime($version->datetime));
                 }
            }
        }
        closedir($dir);
        return $this;
    }

    public function loadVersionFile($file)
    {
        $version = json_decode(
            file_get_contents($file),
            true
        );

        if($version) {
            return $version;
        }
        return false;
    }


    public function getVersionList()
    {
        $list = [];
        $dir = opendir($this->path);
        while($file = readdir($dir)) {
            if($file != '.' && $file !='..') {
                $list[] = $this->path.'/'.$file;
            }
        }
        closedir($dir);
        return $list;
    }

    public function getVersion()
    {
        foreach ($this->getVersionList() as $versionFilepath) {
            yield $this->loadVersionFile($versionFilepath);
        }
    }


    public function save()
    {

        $this->doBeforeSave();
        $versions = $this->source->getVersions();


        $versionFilepath = $this->getVersionFilepath();
        if(!is_dir($versionFilepath)) {
            mkdir($versionFilepath);
        }
        if(!is_file($this->path.'/metadata.json')) {
            file_put_contents(
                $this->path.'/metadata.json',
                json_encode($this->source->getMetadata(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );
        }

        foreach ($versions as $key => $version) {
            $file = $versionFilepath.'/'.$this->source->getName().'.'.$key.'.json';
            if(!is_file($file)) {

                $data = [
                    'metadata' => $this->source->getMetadata(),
                    'version' => $version
                ];

                file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            }

        }


    }

    public function getVersionFilepath()
    {
        return $this->path.'/versions';
    }

}




