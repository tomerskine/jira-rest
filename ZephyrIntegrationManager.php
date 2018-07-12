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
	private $project = ''; //need to hardcode project to operate against. Is it by Fullname, Code, or integer?

	public function synchronizeZephyr($project) {
		$getZephyr = new GetZephyr();
		$zephyrTestList= $getZephyr->getIssuesByProject($project);
		$zephyrTests = $getZephyr->getAllZephyrTests($zephyrTestList);
		// TODO: Will getZephyr manage the querying, looping, and parsing return to create array of Ids or objects?
		$mftfTests = new ParseMFTF();

		$zephyrComparison = new ZephyrComparison($zephyrTests, $mftfTests);
		// $zephyrComparison->setZephyrTests($zephyrTests);
		// $zephyrComparison->setMftfTests($mftfTests);
		//make $zephyr comparison do some work
		$toCreate = $zephyrComparison->getCreateArray();
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