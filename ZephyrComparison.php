<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\JZI;
include_once ('Util/LoggingUtil.php');

class ZephyrComparison {

    /**
     * 2d array of MFTF tests from ParseMFTF class
     * @var array
     */
    public $mftfTests;

    /**
     * array of tests returned from Zephyr
     * @var array 
     */
    public $zephyrTests;
    
    /**
     * Array of tests to be created
     * @var array
     */
    public $createArray;
    
    /**
     * array of MFTF test which need to be created in Zephyr
     * @var array
     */
    public $createArrayByName;
    
    /**
     * array of discrepencies found between MFTF and associated Zephyr test
     * @var array
     */
    public $mismatches;
    
    /**
     * array of tests which hae MFTF <skip> annotation set
     * @var array
     */
    public $skippedTests;

    /**
     * Concatenated string of Story and Title in Zephyr for comparison
     * @var array
     */
    public $zephyrStoryTitle;
    
    public $updateByName;
    
	public $updateById;

    /**
     * Constructor for ZephyrComparison
     * 
     * @param $mftfTests
     * @param $zephyrTests
     */
	public function __construct($mftfTests, $zephyrTests) {
	    $this->mftfTests = $mftfTests;
	    $this->zephyrTests = $zephyrTests;
	    $this->checkForSkippedTests();
        foreach ($this->zephyrTests as $key => $zephyrTest) { // TODO - if test doesnt have story set, it my be created bc no match. Need to exclude from CREATE bc no story to match on.
            if (isset($zephyrTest['customfield_14364'])) {
                $this->zephyrStoryTitle[$key] = $zephyrTest['customfield_14364'] . $zephyrTest['summary'];
            }
            else {
                $this->zephyrStoryTitle[$key] = 'NO STORY ' . $zephyrTest['summary'];
            }
        }
    }

    
    public function matchOnIdOrName() {
	    foreach ($this->mftfTests as $mftfTest) {
	        if (isset($mftfTest['testCaseId'])) {
	            $this->idCompare($mftfTest);
            }
            else {
	            $this->storyTitleCompare($mftfTest);
            }
        }
    }

    public function idCompare($mftfTest)
    {
        $mftfTestCaseId = $mftfTest['testCaseId'][0];
        if (!(array_key_exists($mftfTestCaseId, $this->zephyrTests))) { // IF we can't find the MFTF testCaseID in the zephyr Tests array
            //$this->createArrayById[] = $mftfTest;
            //Array of MFTF tests which have a TestCaseId annotation but the value does not match anything in Zephyr
            //TODO : Resolve this issue. Should this be passed only to the update flow?
            LoggingUtil::getInstance()->getLogger(ZephyrComparison::class)->warn($mftfTestCaseId .
                ' exists as TestCaseId annotation but can not be found in Zephyr. 
                No Integration functions will be run');
        }
        elseif (array_key_exists($mftfTestCaseId, $this->zephyrTests)) { // If we find the MFTF TCID in Zephyr, send the MFTF test and matching zephyr test to check for updates
            $this->testDataComparison($mftfTest, $this->zephyrTests[$mftfTestCaseId], $mftfTestCaseId);
            $this->updateById[] = $mftfTest; // MFTF has TCID and found a match
        }
    }

    public function storyTitleCompare($mftfTest)
    {
        if ((isset($mftfTest['stories'])) && (isset($mftfTest['title']))) { // Check if MFTF has title and story set
            $mftfStoryTitle = $mftfTest['stories'][0] . $mftfTest['title'][0];
            $storyTitleMatch = array_search($mftfStoryTitle, $this->zephyrStoryTitle);
            if (!($storyTitleMatch === false)) {
                $this->updateByName[] = $mftfTest; // MFTF has Story Title and found a match
                $this->testDataComparison($mftfTest, $this->zephyrTests[$storyTitleMatch], $storyTitleMatch);
            }
            else {
                // TODO - DO NOT create anything that doesnt have story set (cf_14364)
                if (isset($mftfTest['severity'])) {
                    $mftfTest['severity'][0] = $this->transformSeverity($mftfTest['severity'][0]);
                }
                $this->createArrayByName[] = $mftfTest; // MFTF has Story Title but has not found a match
            }
        }
        else {
            //$this->updateCheck[] = $mftfTest; // TODO : LOG and handle this case - mftf test did not have both Story and Title set
            $mftfLoggingDescriptor = self::mftfLoggingDescriptor($mftfTest);
            LoggingUtil::getInstance()->getLogger(ZephyrComparison::class)->warn('TEST MISSING STORY OR TITLE ANNOTATIONS: ' . $mftfLoggingDescriptor);
        }

    }

    public function checkForSkippedTests() {
	    foreach ($this->mftfTests as $mftfTest) {
	        if (isset($mftfTest['skip'])) {
	            $this->skippedTests[] = $mftfTest;
            }
        }
    }

	function testDataComparison($mftfTest, $zephyrTest, $key) {
			// check each value against the other using array_diff_assoc
			// Returns the mismatch array giving key=> for mismatches
			// That dont exist exactly in array2 as they do in array1
			//$mismatch[$mftfTest] = array_diff_assoc($mftfTest, $zephyrTest);
        if (isset($mftfTest['description']) && isset($zephyrTest['description'])) {
            if (!($mftfTest['description'][0] == $zephyrTest['description'])) {
                $this->mismatches[$key]['description'] = $zephyrTest['description'];
            }
        }
        elseif (isset($mftfTest['description'])) {
            $this->mismatches[$key]['description'] = $zephyrTest['description'];
        }

       if (isset($mftfTest['title']) && isset($zephyrTest['summary'])) {
           if (!($mftfTest['title'][0] == $zephyrTest['summary'])) {
               $this->mismatches[$key]['summary'] = $mftfTest['title'][0];
           }
       }
       elseif (isset($mftfTest['title'])){
           $this->mismatches[$key]['summary'] = $mftfTest['title'][0];
       }

        if (isset($mftfTest['severity'][0])) {
            $mftfSeverity = $this->transformSeverity($mftfTest['severity'][0]);
            if (isset($zephyrTest['customfield_12720'])){
                if (!($mftfSeverity == $zephyrTest['customfield_12720']['value'])) {
                    $this->mismatches[$key]['severity'] = $mftfSeverity;
                }
            }
            else {
                $this->mismatches[$key]['severity'] = $mftfSeverity;
            }
        }

        if (isset($mftfTest['stories']) && isset($zephyrTest['customfield_14364'])) {
            if (!($mftfTest['stories'][0] == $zephyrTest['customfield_14364'])) {
                $this->mismatches[$key]['stories'] = $mftfTest['stories'][0];
            }
        }
        elseif (isset($mftfTest['stories'])) {
            $this->mismatches[$key]['stories'] = $mftfTest['stories'][0];
        }

        if ((isset($mftfTest['skip'])) && (!($zephyrTest['status']['name'] == "Skipped"))) {
            $this->mismatches[$key]['skip'] = $mftfTest['skip'][0]; // TODO : do we need to handle multiple skip associated Ids?
        }

        if (!(isset($mftfTest['skip'])) && ($zephyrTest['status']['name'] == "Skipped")) {
            $this->mismatches[$key]['unskip'] = TRUE; 
        }

        if (isset($this->mismatches[$key])) {
            $this->mismatches[$key]['status'] = $zephyrTest['status']['name'];
        }


        // If mftf and zpehyrn fields are a MATCH then:
        // 1. do NOT send that field to the UPDATE
        // 2. Instead, remove that field entirely from the update array
        // 3. The updateIssue will then only populate fields as issue->type('value') from the array where the key isset
		}

//	function makeZephyrUpdates($mismatches){
//		foreach ($mismatches as $mftfUpdateData) {
//			updateIssue::updateIssue($mftfUpdateData);
//		}
    public function transformSeverity($mftfSeverity) {
        switch ($mftfSeverity) {
            case "BLOCKER" :
                $mftfSeverity = '0-Blocker';
                break;
            case "CRITICAL" :
                $mftfSeverity = '1-Critical';
                break;
            case "NORMAL" :
                $mftfSeverity = '2-Major';
                break;
            case "MINOR" :
                $mftfSeverity = '3-Average';
                break;
            case "TRIVIAL" :
                $mftfSeverity = '4-Minor';
                break;
        }
        return $mftfSeverity;
    }

    public static function mftfLoggingDescriptor($mftfTest) {
        if (isset($mftfTest['testCaseId'])) {
            $mftfLoggingDescriptor = $mftfTest['testCaseId'][0];
        }
        elseif (isset($mftfTest['stories']) && isset($mftfTest['title'])) {
            $mftfLoggingDescriptor = $mftfTest['stories'][0] . $mftfTest['title'][0];
        }
        elseif (isset($mftfTest['title'])) {
            $mftfLoggingDescriptor = $mftfTest['title'][0];
        }
        else {
            $mftfLoggingDescriptor = 'NO STORY OR TITLE SET ON TEST';
        }
        return $mftfLoggingDescriptor;
    }

	function setZephyrTests($zephyrTests){
	    $this->zephyrTests = $zephyrTests;
    }

    function setMFTFTests($mftfTests){
	    $this->mftfTests = $mftfTests;
    }

	function getCreateArrayById(){
		return $this->createArrayById;
	}

	function getCreateArrayByName()
    {
        return $this->createArrayByName;
    }

    function getUpdateCheck() {
	    return $this->updateCheck;
    }

    function getUpdateArray(){
        return $this->mismatches;
    }

    function getSkippedTests() {
	    return $this->skippedTests;
    }

//    public function simpleCompare()
//    {
//        foreach ($this->mftfTests as $mftfTest) {
//            if (array_key_exists($mftfTest['testCaseId'], $this->zephyrTests)) { //id compare - does the mftf testcase ID exist as a zephyr key
//                $this->createArrayById[] = $mftfTest['testCaseId'];
//            }
//        }
//        foreach ($this->zephyrTests as $zephyrTest) {
//            $zephyrTestCaseId = $zephyrTest['testCaseId'][0];
//            if (array_key_exists($zephyrTestCaseId, $this->mftfTests)) {
//                $this->createArrayById = $zephyrTestCaseId;
//            }
//        }
//        return $this->createArrayById;
//    }
//
//
//    function existenceCheck()
//    {
//        foreach ($this->mftfTests as $mftfTest) {
//            foreach ($this->zephyrTests as $zephyrTest) {
//                if (!(array_key_exists($mftfTest['testCaseId'], $zephyrTest)) && (isset($mftfTest['testCaseId']))) {
//                    if (!($mftfTest['testCaseId'] == $zephyrTest['issueId'])) {
//                        $this->createArrayById[] = $mftfTest;
//                    }
//                } elseif (isset($mftfTest['Title']) && isset($mftfTest['Story'])) {
//                    if (!($mftfTest['stories'].$mftfTest['title'] == $zephyrTest['stories'].$zephyrTest['title'])) {
//                        $this->createArrayByName[] = $mftfTest;
//                    }
//                } else {
//                    $this->mismatches[] = $mftfTest; // For any mismatch, overwrite all fields with MFTF data
//                    // $mismatches[] = $this->testDataComparison($mftfTest, $zephyrTests[$mftfTest]); // Find exact mismatch and only update those fields
//
//                }
//            }
//        }
//    }

}