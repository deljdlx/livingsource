<?php

use JLB\LivingSource\Reader;

require(__DIR__.'/../source/class/Reader.php');


$reader = new Reader(__DIR__.'/../index.html.versionned.json');

foreach ($reader->getVersionsIndexes() as $index) {
    $version = $reader->getVersionByIndex($index);

    echo $version['content'];
    echo '<hr/>';
}


