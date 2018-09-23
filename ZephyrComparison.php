<?php

namespace Magento\JZI;

class ZephyrComparison {

	public $mftfTests;
	// array of test objects as returned from MFTF TestObjectHandler
	public $zephyrTests;
	// array of zephyr test data from API call to zephyr
	public $createArray;
	// array of tests which do not yet exist in Zephyr so must be created
	public $toCompare;
	public $createArrayById;
	public $createArrayByName;
	public $mismatches;
	public $updateCheck;
	public $skippedTests;
	public $zephyrStoryTitle;
	public $updateByName;
	public $updateById;
	// array of tests which do exist in Zephyr but need to be compared for updates
    //public $createArrayByName;

	public function __construct($mftfTests, $zephyrTests) {
	    $this->mftfTests = $mftfTests;
	    $this->zephyrTests = $zephyrTests;
	    $this->checkForSkippedTests();
        foreach ($this->zephyrTests as $zephyrTest) {
            $this->zephyrStoryTitle[] = $zephyrTest['customfield_14364'] . $zephyrTest['summary'];
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
        if (!(array_key_exists($mftfTestCaseId, $this->zephyrTests))) {
            $this->createArrayById[] = $mftfTest;
            //Array of MFTF tests which have a TestCaseId annotation but the value does not match anything in Zephyr
            //TODO : Resolve this issue. Should this be passed only to the update flow?
            LoggingUtil::getInstance()->getLogger(ZephyrComparison::class)->warn($mftfTestCaseId .
                ' exists as TestCaseId annotation but can not be found in Zephyr. 
                No Integration functions will be run');
        }
        elseif (array_key_exists($mftfTestCaseId, $this->zephyrTests)) {
            $this->updateById[] = $mftfTest; // MFTF has TCID and found a match
        }
    }

    public function storyTitleCompare($mftfTest)
    {
        if ((isset($mftfTest['stories'])) && (isset($mftfTest['title']))) {
            $mftfStoryTitle = $mftfTest['stories'][0] . $mftfTest['title'][0];
            if (array_search($mftfStoryTitle, $this->zephyrStoryTitle)) {  // TODO : ARRAY SEARCH DOESNT WORK BECAUSE ZEPHYRSTORTITLE ISNT AN ARRAY
                $this->updateByName[] = $mftfTest; // MFTF has Story Title and found a match
                array_search($mftfStoryTitle, $this->zephyrStoryTitle);
            }
            else {
                $this->createArrayByName[] = $mftfTest; // MFTF has Story Title but has not found a match
            }
        }
        else {
            $this->updateCheck[] = $mftfTest; // TODO : LOG and handle this case - mftf test did not have both Story and Title set
            LoggingUtil::getInstance()->getLogger(ZephyrComparison::class)->warn('TEST MISSING STORY OR TITLE ANNOTATIONS: ' . $mftfTest);
        }

    }

    public function checkForSkippedTests() {
	    foreach ($this->mftfTests as $mftfTest) {
	        if (isset($mftfTest['skip'])) {
	            $this->skippedTests[] = $mftfTest;
            }
        }
    }

    public function simpleCompare()
    {
        foreach ($this->mftfTests as $mftfTest) {
            if (array_key_exists($mftfTest['testCaseId'], $this->zephyrTests)) { //id compare - does the mftf testcase ID exist as a zephyr key
                $this->createArrayById[] = $mftfTest['testCaseId'];
            }
        }
        foreach ($this->zephyrTests as $zephyrTest) {
            $zephyrTestCaseId = $zephyrTest['testCaseId'][0];
            if (array_key_exists($zephyrTestCaseId, $this->mftfTests)) {
                $this->createArrayById = $zephyrTestCaseId;
            }
        }
        return $this->createArrayById;
    }


    function existenceCheck()
    {
        foreach ($this->mftfTests as $mftfTest) {
            foreach ($this->zephyrTests as $zephyrTest) {
                if (!(array_key_exists($mftfTest['testCaseId'], $zephyrTest)) && (isset($mftfTest['testCaseId']))) {
                    if (!($mftfTest['testCaseId'] == $zephyrTest['issueId'])) {
                        $this->createArrayById[] = $mftfTest;
                    }
                } elseif (isset($mftfTest['Title']) && isset($mftfTest['Story'])) {
                    if (!($mftfTest['stories'].$mftfTest['title'] == $zephyrTest['stories'].$zephyrTest['title'])) {
                        $this->createArrayByName[] = $mftfTest;
                    }
                } else {
                    $this->mismatches[] = $mftfTest; // For any mismatch, overwrite all fields with MFTF data
                    // $mismatches[] = $this->testDataComparison($mftfTest, $zephyrTests[$mftfTest]); // Find exact mismatch and only update those fields

                }
            }
        }
    }

	function testDataComparison($mftfTest, $zephyrTest){
			// check each value against the other using array_diff_assoc
			// Returns the mismatch array giving key=> for mismatches
			// That dont exist exactly in array2 as they do in array1
			//$mismatch[$mftfTest] = array_diff_assoc($mftfTest, $zephyrTest);
        if (!($mftfTest['description'][0] == $zephyrTest['fields']['description'])) {
            $this->mismatches[key($zephyrTest)]['description'] = $mftfTest['description'][0];
        }
        if (!($mftfTest['title'][0] == $zephyrTest['fields']['summary'])) {
            $this->mismatches[key($zephyrTest)]['summary'] = $mftfTest['description'][0];
        }
        if (isset($mftfTest['severity'][0])) {
            $mftfSeverity = $this->transformSeverity($mftfTest['severity'][0]);
            if (!($mftfSeverity == $zephyrTest['fields']['severity'])){
                $this->mismatches[key[$zephyrTest]['severity']] = $mftfSeverity;
                }
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
                $mftfSeverity = '0 - Blocker';
                break;
            case "CRITICAL" :
                $mftfSeverity = '1 - Critical';
                break;
            case "MAJOR" :
                $mftfSeverity = '2 - Major';
                break;
            case "AVERAGE" :
                $mftfSeverity = '3 - Average';
                break;
            case "MINOR" :
                $mftfSeverity = '4 - Minor';
                break;
        }
        return $mftfSeverity;
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
//
//	function gettoCompare(){
//		return $this->$toCompare;
//	}

}