<?php

namespace Magento\JZI;

//TODO: clean up autoload to avoid hardcoding dir traversal
//require(__DIR__ . "/../../../../../autoload.php");

use \Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use \Closure;

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
        // TODO : CHECK FOR ALL REQUIRED ANNOTATIONS
        // 1. CHECK THAT ALL ANNOTATIONS IN MFTFARRAY EXIST
        // 2. IF NOT SET, IF POSSIBLE, SET DEFAULT VALUE TO WRITE TO ZEPHYR. LOG ERROR AND DEFAULT WRITE.
        // 3. IF NOT SET AND NOT POSSIBLE, LOG ERROR AS UNWRITEABLE
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




