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
    private $jql = '';  // Allow invocation to directly pass jql to get Zephyr issues for match
    //TODO: How will this work with Zephyr subset and full tests? How will we prevent CREATE against filter excluded but existing tests

	public function synchronizeZephyr($project) {
		$getZephyr = new GetZephyr();
		$zephyrTests= $getZephyr->getIssuesByProject($project); // This now returns annotation array
		//$zephyrTests = $getZephyr->getAllZephyrTests($zephyrTestList); // This is no longer needed
		// TODO: Will getZephyr manage the querying, looping, and parsing return to create array of Ids or objects?
        $parseMFTF = new ParseMFTF();
        $mftfTests = $parseMFTF->getTestObjects();

		$zephyrComparison = new ZephyrComparison($zephyrTests, $mftfTests);
//		$toCreate = $zephyrComparison->getCreateArrayById();
//		$toUpdate = $zephyrComparison->getUpdateArray();
        $createVerify = $zephyrComparison->simpleCompare(); //simpleCompare CreateArrayById
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