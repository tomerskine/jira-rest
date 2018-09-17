<?php

namespace Magento\JZI;

require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\JiraException;

class createIssue{
	
	public $test;

	function __construct($id){
		$this->test = $id;
	}

	function createIssuesREST($test){
		//need to make multiple payloads for mass create issue
		//need each test as seperate array of tuples
		try {
		    $issueField = new IssueField();

		    $issueField->setProjectKey($createArray['Project']) //No project in MFTF array
		                ->setSummary($createArray['Summary']) // No summary in MFTF array
		                ->setAssigneeName($createArray['AssigneeName']) // Have to assign to no one - how?
		                ->setPriorityName($createArray['priority']) // need to REMOVE (set as NULL) and use the customField
		                ->setIssueType('Test') // OK
		                ->setDescription($createArray['Description']) // description
		                ->addVersion($createArray['version']) // version?
		                ->addComponents(['', '']) // ??
		                // set issue security if you need.
		                //->setSecurityId(10001 /* security scheme id */)
		                ->setDueDate('')
                        // Add custom Field mappings
		            ;
			
		    $issueService = new IssueService();

		    $ret = $issueService->create($issueField);
		    //$ret = $issueService->createMultiple([$issueFieldOne, $issueFieldTwo]);
			
		    //If success, Returns a link to the created issue.
		    var_dump($ret);
		} catch (JiraException $e) {
			print("Error Occured! " . $e->getMessage());
		}

	}

	function getErrors(){
	    return 5;
    }
}