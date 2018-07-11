<?php

require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\JiraException;

class createIssue{
	
	//public $zephyrComparrisson;

	function __construct(){
		$this->zephyrComparrisson = new zephyrComparrisson;
	}

	function createIssuesREST($createArray){
		//need to make multiple payloads for mass create issue
		//need each test as seperate array of tuples
		try {
		    $issueField = new IssueField();

		    $issueField->setProjectKey($createArray['Project'])
		                ->setSummary($createArray['Summary']
		                ->setAssigneeName($createArray['AssigneeName'])
		                ->setPriorityName($createArray['priority'])
		                ->setIssueType('Test')
		                ->setDescription($createArray['Description'])
		                ->addVersion($createArray['version'])
		                ->addComponents(['', ''])
		                // set issue security if you need.
		                //->setSecurityId(10001 /* security scheme id */)
		                ->setDueDate('')
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
}