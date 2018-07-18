<?php

class ZephyrIntegrationManager {
	/**
	*Purpose of Manager
	* 1. call GetZephyrTest and store resulting json
	* 2. get MFTF test data from parsers
	* 3. call ZephyrComparison to build list of Create and Update
	* 4. call Creates
	* 5. call Updates
	* 6. Log returns and created IDs
	* 7. Log errors (TODO: manage retries)
	*/

    /**
     * Can hardcode project or use setter
     * This is used as part of a JQL query to filter tests to project scope
     *
     * @var string
     */
	private $project = 'MC'; //same as JQL search

	public function synchronizeZephyr($project) {
		$getZephyr = new GetZephyr();
		$zephyrTestList= $getZephyr->prototypeGetIssuesByProject();
		$zephyrTests = $getZephyr->prototypeGetAllZephyrTests();
		// TODO: Will getZephyr manage the querying, looping, and parsing return to create array of Ids or objects?

		$protoypeArrays = new GetPrototypeArrays;
		$mftfTests = $protoypeArrays->getMatchingArray();
		//$mftfTests = $protoypeArrays->getNoTestCaseIDArray(); // Uncomment for: No testCaseID BUT has match on story title
        //$mftfTests = $prototypeArrays->getNewTestArray();     // Uncomment for: MFTF test is new and will be created

		$zephyrComparison = new ZephyrComparison($zephyrTests, $mftfTests);
		$toCreate = $zephyrComparison->getCreateArrayById();
		$toUpdate = $zephyrComparison->getUpdateArray();
		$created = new CreateIssue($toCreate);
		$updated = new UpdateIssue($toUpdate);
		$createErrors = $created->getErrors();
		$updateErrors = $updated->getErrors();
		// Write $created, $updated, $createErrors, and $updateErrors to file (or var_dump for now)
	}

	public function setProject($project) {
	    $this->project = $project;
    }

}