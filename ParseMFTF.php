<?php

require 'vendor/autoload.php';

use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;


//class ParseMFTF {

    function getTestObjects() {
        $testObjects = TestObjectHandler::getInstance()->getAllObjects();
        return $testObjects;
    }
//}

$testObjects = getTestObjects();
var_dump($testObjects);
