<?php

require 'vendor/autoload.php';

use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;


//class ParseMFTF {

    function getTestObjects() {
        //$testObjects = TestObjectHandler::getInstance()->getAllObjects();
        $testObjects = file_get_contents(__DIR__ . '/samplejson/testObjects.json');
        //print_r($json);
        //testObjects = json_decode($json);
        //print_r('hello');
        print_r($testObjects[0]);
        print_r($testObjects[2]);
        return $testObjects;
    }
//}

$testObjects = getTestObjects();
//var_dump($testObjects);
