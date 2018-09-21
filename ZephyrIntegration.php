<?php

namespace Magento\JZI;


require(__DIR__ . "/../../../vendor/autoload.php");
use Magento\JZI\GetZephyr;
include ('GetZephyr.php');
include ('ParseMFTF.php');
include ('ZephyrComparison.php');
include ('CreateManager.php');

ini_set('memory_limit', '512M');

function debugOutputArrays()
{
    $getZephyr = new \Magento\JZI\GetZephyr();
    $zephyrTests = $getZephyr->getIssuesByProject('MC');
    $zephyrTests = array_slice($zephyrTests, 0, 5);
    //print_r($zephyrTests);
    print("----------------------------\n");
    print("----------------------------\n");
    print("----------------------------\n");
    print("----------------------------\n");


    $parseMFTF = new ParseMFTF();
    $mftfTests = $parseMFTF->getTestObjects();
    $mftfTests = array_slice($mftfTests, 1, 3);
    //print_r($mftfTests);

    $zephyrComparison = new ZephyrComparison($mftfTests, $zephyrTests);
    $createVerify = $zephyrComparison->matchOnIdOrName();
    $createById = $zephyrComparison->getCreateArrayById();
    $createByName = $zephyrComparison->getCreateArrayByName();
    $skippedTests = $zephyrComparison->checkForSkippedTests();
    print_r($createById);
    print("***************\n****************\n*******************\n");
    print_r($createByName);
    print("***************\n****************\n*******************\n");
    print_r($skippedTests);

//    $createManager = CreateManager::getInstance();
//    $createResponse = $createManager->performCreateOperations($createByName, $createById, $skippedTests);
//
//
//    $createManager = new CreateManager::getInstance($createByName, $createById, $skippedTests)->preformCreateOperations;
//    $updateHandler = new UpdateHandler::getInstance($updates, $skippedTests)->performUpdateOperations;
    }

//function dryRunOutputs() {
$getZephyr = new \Magento\JZI\GetZephyr();
$zephyrTests = $getZephyr->getIssuesByProject('MC');
//$zephyrTests = array_slice($zephyrTests, 0, 5);

$parseMFTF = new ParseMFTF();
$mftfTests = $parseMFTF->getTestObjects();
//$mftfTests = array_slice($mftfTests, 1, 3);

$zephyrComparison = new ZephyrComparison($mftfTests, $zephyrTests);
$zephyrComparison->matchOnIdOrName();
$createById = $zephyrComparison->getCreateArrayById();
$createByName = $zephyrComparison->getCreateArrayByName();
$skippedTests = $zephyrComparison->checkForSkippedTests();

$dryRunCreate = CreateManager::getInstance()->performDryRunCreateOperations($createByName, $createById, $skippedTests);
//$dryRunCreate = CreateManager::getInstance()->performDryRunCreateOperations($createByName, $createById, $skippedTests);
//print_r($dryRunCreate);
//testing composer update

