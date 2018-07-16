<?php

include __DIR__ . '/../autoload.php';

use \vendor\Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;


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