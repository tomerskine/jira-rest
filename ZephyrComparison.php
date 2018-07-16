<?php

class ZephyrComparison{

	public $mftfTests;
	// array of test objects as returned from MFTF TestObjectHandler
	public $zephyrTests;
	// array of zephyr test data from API call to zephyr
	public $createArray;
	// array of tests which do not yet exist in Zephyr so must be created
	public $toCompare;
	public $createArrayById;
	public $createArrayByName;
	// array of tests which do exist in Zephyr but need to be compared for updates
    //public $createArrayByName;
	
	function doComparison(){
		existenceCheck;
		createZephyrTests;
		updateZephyrTests;
	}

	public function __construct($mftfTests, $zephyrTests) {
	    $this->mftfTests = $mftfTests;
	    $this->zephyrTests = $zephyrTests;

    }

    function existenceCheck(){
		foreach ($this->mftfTests as $mftfTest) {
			if (array_key_exists($mftfTest['testCaseId'], $mftfTest) && (isset($mftfTest['testCaseId']))) {
				if (!($mftfTest['testCaseId'] == $zephyrTest['issueId'])) {
                    $this->createArrayById[] = $mftfTest['testCaseId'];
                }
			}
			elseif (isset($mftfTest['Title']) && isset($mftfTest['Story'])) {
                if (!($mftfTest['testCaseId'] == $zephyrTest['issueId'])) {
                    $this->createArrayByName[] = $mftfTest['story'] . $mftfTest['title'];
                }
			}
			else{
				$mismatches[] = $this->testDataComparison($mftfTest, $zephyrTests[$mftfTest]);

			}
		}	
	}

	function testDataComparison($mftfTest, $zephyrTest){
//			if (!array_diff_key($mftfTest, $zephyrTest)){
//				// throw an error. This occurs if mftf and zpehyr do not have tte same keys.
//			}
			// check each value against the other using array_diff_assoc
			// Returns the mismatch aray giving key=> for mismatches
			// That dont exist exactly in array2 as they do in array1
			$mismatch = array_diff_assoc($mftfTest, $zephyrTest);
        return $mftfTest;
		}

	function makeZephyrUpdates($mismatches){
		foreach ($mismatches as $mftfUpdateData) {
			updateIssue::updateIssue($mftfUpdateData);
		}
	}

	function setZephyrTests($zephyrTests){
	    $this->zephyrTests = $zephyrTests;
    }

    function setMFTFTests($mftfTests){
	    $this->mftfTests = $mftfTests;
    }

	function getCreateArrayByName(){
		return $this->createArrayById;
	}

	function getCreateArrayByName() {
	    return $this->createArrayByName;
    }

    function getUpdateArray(){
        return $this->$updatesArray;
    }

	function gettoCompare(){
		return $this->$toCompare;
	}

//	function keys_are_equal($potentialUpdate, $zephyrTests) {
//		return !array_diff_key($array1, $array2) && !array_diff_key($array2, $array1);
//	}
}