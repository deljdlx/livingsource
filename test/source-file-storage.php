<?php

use ElBiniou\LivingSource\Source;
use ElBiniou\LivingSource\SourceFileStorage;


require(__DIR__.'/../bootstrap.php');


$source = new Source(__FILE__);

$storage = new SourceFileStorage($source, __DIR__.'/versionned.json');


$source->createVersion();
$storage->save($source);



foreach ($storage->getVersion() as $version) {
    print_r($version);
    echo "\n======================================================================\n";
}

