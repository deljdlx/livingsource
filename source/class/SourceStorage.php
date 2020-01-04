<?php

namespace ElBiniou\LivingSource;


abstract class SourceStorage
{

    /**
     * @var Source
     */
    protected $source;

    public abstract function loadVersions();
    public abstract function save();


    public function __construct(Source $source)
    {
        $this->source = $source;
    }

    protected function doBeforeSave()
    {
        if (empty($this->source->getVersions())) {
            $this->source->createVersion('Initial version', 'Initial version');
        }
    }



}



