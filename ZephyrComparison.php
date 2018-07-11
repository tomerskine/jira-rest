<?php

class ZephyrComparison{

	public $mftfTests;
	// array of test objects as returned from MFTF TestObjectHandler
	public $zephyrTests;
	// array of zephyr test data from API call to zephyr
	public $createArray;
	// array of tests which do not yet exist in Zephyr so must be created
	public $toCompare;
	// array of tests which do exist in Zephyr but need to be compared for updates
	
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
		foreach $mftfTests as $mftfTest{
			if isset($mftfTest['issueId'] && array_key_exists($mftfTest['issueId'], $zephyrTests)){
				if $mftfTest['issueId'] == $zephyrTest['issueId'] // break;, it exists
				$this->$createArrayById[] = $mftfTest['name'];
			}
			else if (isset($mftfTest['Title']) && isset($mftfTest['Story']) && array_key_exists(huh?)) {
				$this->$createArrayByName[] = $mftfTest['story'].$mftfTest['title'];
			}
			else{
				// $this->$toCompare[] = $mftfTest['name'];
				$this->$mismatches[] = testDataComparison($mftfTest, $zephyrTests[$mftfTest])

			}
		}	
	}

	function testDataComparison($mftfTest, $zephyrTest){
			if (!array_diff_key($mftfTest, $zephyrTest)){
				// throw an error. This occurs if mftf and zpehyr do not have hte same keys.
			}
			// check each value against the other using array_diff_assoc
			// Returns the mismatch aray giving key=> for mismatches
			// That dont exist exactly in array2 as they do in array1
			$mismatch = array_diff_assoc($mftfTest, $zephyrTest);
			if (isset($mismatch){
				return $mftfTest;
			}
		}

	function makeZephyrUpdates($mismatches){
		foreach $mismatches as $mftfUpdateData{
			updateIssue::update($mftfUpdateData);
		}
	}

	function setZephyrTests($zephyrTests){
	    $this->zephyrTests = $zephyrTests;
    }

    function setMFTFTests($mftfTests){
	    $this->mftfTests = $mftfTests;
    }

	function getCreateArray(){
		return $this->$createArray;
	}

    function getUpdateArray(){
        return $this->$updatesArray;
    }

	function gettoCompare(){
		return $this->$toCompare;
	}

	function keys_are_equal($potentialUpdate, $zephyrTests) {
		return !array_diff_key($array1, $array2) && !array_diff_key($array2, $array1);
	}
}