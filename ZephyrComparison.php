<?php

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
	// array of tests which do exist in Zephyr but need to be compared for updates
    //public $createArrayByName;

	public function __construct($mftfTests, $zephyrTests) {
	    $this->mftfTests = $mftfTests;
	    $this->zephyrTests = $zephyrTests;

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
        }
        else {
            $this->updateCheck[] = $mftfTest;
        }
    }

    public function storyTitleCompare($mftfTest)
    {
        if ((isset($mftfTest['stories'])) && (isset($mftfTest['title']))) {
            $mftfStoryTitle = $mftfTest['stories'][0] . $mftfTest['title'][0];
        }
        foreach ($this->zephyrTests as $zephyrTest) {  // BUILD THIS ARRAY ONCE ONLY IN matchOnIdOrName
                $zephyrStoryTitle[] = $zephyrTest['customfield_14364'] . $zephyrTest['summary'];
                }
        if (!(array_search($mftfStoryTitle, $zephyrStoryTitle))) {
            $this->createArrayByName[] = $mftfTest;
        }
        else {
            $this->updateCheck[] = $mftfTest;
        }

    }

    public function checkForSkippedTests() {
	    foreach ($this->mftfTests as $mftfTest) {
	        if (isset($mftfTest['skip'])) {
	            $this->skippedTests[] = $mftfTest;
            }
        }
        return $this->skippedTests;
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
			$mismatch[$mftfTest] = array_diff_assoc($mftfTest, $zephyrTest);
        return $mismatch;
		}

//	function makeZephyrUpdates($mismatches){
//		foreach ($mismatches as $mftfUpdateData) {
//			updateIssue::updateIssue($mftfUpdateData);
//		}

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
//
//	function gettoCompare(){
//		return $this->$toCompare;
//	}

}