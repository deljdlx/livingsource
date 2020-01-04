<?php
require(__DIR__.'/../bootstrap.php');

use ElBiniou\LivingSource\Listener;
use ElBiniou\LivingSource\Source;


use Phi\Console\Command;
use Phi\Console\CommandFilter;
use Phi\Console\Option;



$command = new Command();

$sourcePathOption = new Option('sourcePath');
$sourcePathOption->addAlias('-s');
$command->addOption($sourcePathOption);


$outputPathOption = new Option('outputPath');
$outputPathOption->addAlias('-o');
$command->addOption($outputPathOption);


$command->addFilter(new CommandFilter(function(Command $command) {

}));


$command->setMain(function($command) {
    $sourcePath = $command->getOptionValue('sourcePath');
    $outputPath = $command->getOptionValue('outputPath');

    echo "Starting to listen ".realpath($sourcePath)."\n";

    $listener = new Listener($sourcePath);
    $listener->register(function($file, $type) {

        if($type === Listener::EVENT_NEW) {
            return;
        }

        echo "Changes in ".$file."\n";

        //$output =__DIR__.'/../diff.versionned.json';
        //$source = new Source($file, $output);
        //$source->createVersion();
        //$source->save();
    });
    
    $listener->listen();

});


$command->execute();



exit();

$path = __DIR__.'/../viewer';



/*
$source = new Source('index.html');
$source->createVersion();
$source->save('output.version.json');
*/
