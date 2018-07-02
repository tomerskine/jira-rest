<?php

class zephyrComparisson{

	public $mftfTests;
	public $zephyrTests;
	public $createArray;
	public $potentialUpdates;
	
	function compareZephyrToMFTF($zephyrTests, $mftfTests){
		foreach $mftfTests as $mftfTest{
			if (in_array($mftfTest, $zephyrTests){
				$this->$createArray[] = $mftfTest;
			}
			else{
				$this->$potentialUpdates[] = $mftfTest;
			}
		return $createArray;
		}

	}

	function getCreateArray(){
		return $this->$createArray;
	}

	function getPotentialUpdates(){
		return $this->$potentialUpdates;
	}
}