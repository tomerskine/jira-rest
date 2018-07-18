<?php

require(__DIR__ . "/../../../vendor/autoload.php");

use \Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;


class ParseMFTF {

    function getTestObjects() {
        $testObjects = TestObjectHandler::getInstance()->getAllObjects();
//        //$testObjects = file_get_contents(__DIR__ . '/samplejson/testObjects.json');
//        //print_r($json);
//        //testObjects = json_decode($json);
//        //print_r('hello');
//        //print_r($testObjects[0]);
//        //print_r($testObjects[2]);
        return $testObjects;
    }
}

$testObjects = getTestObjects();
var_dump($testObjects);

//
//class ParseMFTF {
//
//    function getTestObjects() {
//        $testObjects = array(
//            'features' => 'cms',
//            'stories' => 'MAGETWO-42156-Widgets in WYSIWYG',
//            'group' => 'Cms',
//            'title'  => 'Admin should be able to create a CMS page with widget type: Catalog product list',
//            'description' => 'Admin should be able to create a CMS page with widget type: Catalog product list',
//            'severity' => 'CRITICAL',
//            'testCaseId' => 'MAGETWO-67091'
//        );
//        return $testObjects;
//    }
//}


class GetPrototypeArrays {

    public function getMatchingArray() {
        $mftfTests = ["features" => "prototypeFeature", "stories" => "prototypeStory", "title" => "prototypeTitle", "description" => "prototypeDescription", "testCaseId" => "TOM-123"];
        return $mftfTests;
    }
    public function getNoTestCaseIDArray() {
        $mftfTests = ["features" => "prototypeFeature", "stories" => "prototypeStory", "title" => "prototypeTitle", "description" => "prototypeDescription"];
        return $mftfTests;
    }

    public function getNewTestArray() {
        $mftfTests = ["features" => "prototypeFeatureNEW", "stories" => "prototypeStoryNEW", "title" => "prototypeTitleNEW", "description" => "prototypeDescriptionNEW"];
        return $mftfTests;
    }
}


$autoload = require(__DIR__ . "/../../../vendor/autoload.php");
$testGen = TestObjectHandler::getInstance()->getAllObjects();
//$catIds = array_column($testGen, 'id');
//$catIdsmap = array_map(create_function('$o', 'return $o->id;'), $testGen);
$catIdsmap = array_map(function($o) { return $o->annotations; }, $testGen);
var_dump($catIdsmap);

// Find MFTF (via vendor or otherwise)
// Load _bootstrap.php (mftf/M/F/_bootstrap.php (should be able to detect where youre running it from
// call testObejctHandler, get object back for use
//$testObjects = ParseMFTF::getTestObjects();
//var_dump($testObjects);


