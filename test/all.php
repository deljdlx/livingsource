<?php

use JLB\LivingSource\Listener;
use JLB\LivingSource\Source;

require(__DIR__.'/../source/class/Source.php');
require(__DIR__.'/../source/class/Listener.php');



//$path = realpath(__DIR__.'/../../teaching/promotion/2020-01-crusoe/runtime/s02/e02/S02-E01-challenge-script-php-en-ldc');
/*
 * bon voyons voir....
 */

$path = __DIR__.'/../viewer';

//ok ça a l'air de marcher
$listener = new Listener($path);
$listener->register(function($file, $type) {

    if($type === Listener::EVENT_NEW) {
        return;
    }

    echo "in callback ".$file."\n";


    //$output = __DIR__.'/'.basename($file).'.versionned.json';

    $output =__DIR__.'/../diff.versionned.json';
    $source = new Source($file, $output);
    $source->createVersion();
    $source->save();



});


$listener->listen();

/*
$source = new Source('index.html');
$source->createVersion();
$source->save('output.version.json');
*/
