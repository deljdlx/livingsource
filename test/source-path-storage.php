<?php

use ElBiniou\LivingSource\Source;
use ElBiniou\LivingSource\SourceFileStorage;
use ElBiniou\LivingSource\SourcePathStorage;


require(__DIR__.'/../bootstrap.php');


$source = new Source(__FILE__);

$storage = new SourcePathStorage($source, __DIR__.'/output');

$source->createVersion();
$storage->save($source);

foreach ($storage->getVersion() as $version) {
    print_r($version);
    echo "\n======================================================================\n";
}



