<?php

//TODO: clean up autoload to avoid hardcoding dir traversal
require(__DIR__ . "/../../../../../autoload.php");

use \Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;

class ParseMFTF {

    public function getTestObjects() {
        $testObjects = TestObjectHandler::getInstance()->getAllObjects();
        foreach ($testObjects as $test) {
            $propGetter = Closure::bind(function($prop){return $this->$prop;}, $test, $test );
            $annotations[] = $propGetter('annotations');
        }
//        foreach ($annotations as $annotation) {
//            if (isset($annotation['title'])){
//                print_r($annotation['title']);
//            }
//        }
        return $annotations;
    }
}

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




