<?php

namespace Magento\JZI;

//require 'vendor/autoload.php';

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
		//need each test as separate array of tuples
		try {
		    $issueField = new IssueField();

		    $issueField->setProjectKey($test['Project']) //TODO: No project in MFTF array
		                ->setSummary($test['title'][0]) // Use Title from MFTF array
		                ->setAssigneeName($test['AssigneeName']) // TODO: choose assignee
		                ->setIssueType('Test') // OK
		                ->setDescription($test['description'][0]) // OK
		                // ->addVersion($test['version']) // version?
		                // ->addComponents(['', '']) // MFTF does not record Components -- TODO: is component used for reporting?
		                // set issue security if you need.
		                //->setSecurityId(10001 /* security scheme id */)
		                //->setDueDate('')
                        // 'customfield_14362', implode("', '", $test['group'])) // have to implode any customfield that will use multiple values (strings)
                        // Add custom Field mappings
                        ->addCustomField('customfield_14364', $test['stories'][0])
                        ->addCustomField('customfield_14362', implode("', '", $test['group'][0])) // have to implode any customfield that will use multiple values (strings)
                        ->addCustomField('customfield_12720', ['value' => $test['severity'][0]])
                        // TODO: for any customfields taking LIST, need to ['value' => 'foo'] and [ ['value => 'foo'], ['value' => 'bar'] ] or multiple list selections
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

	function createSkippedTest($test) {
	    //do skipped
    }

	function getErrors(){
	    return 5;
    }

    // TODO : REMOVE
    static function createDryRunIssuesREST($test)
    {
    //need to make multiple payloads for mass create issue
    //need each test as separate array of tuple
        // TODO : MOVE TO FUNCTION
        $stories = (isset($test['stories'])) ? $test['stories'] : 'NO STORY';
        $severity = (isset($test['severity'])) ? $test['severity'] : '2-major';
        $group = 'DEFAULT GROUP VALUE'; // TODO : NEED TO AGREE ON DEFAULT VALUE - POSSIBLY CATALOG ?

        $issueField = new IssueField();

        $issueField->setProjectKey('TOMERSKINE')//TODO: No project in MFTF array
        ->setSummary($test['title'][0])// Use Title from MFTF array
        ->setAssigneeName('tomerskine')// TODO: choose assignee
        ->setIssueType('Test')// OK
        ->setDescription($test['description'][0])// OK
        // ->addVersion($test['version']) // version?
        // ->addComponents(['', '']) // MFTF does not record Components -- TODO: is component used for reporting?
        // set issue security if you need.
        //->setSecurityId(10001 /* security scheme id */)
        //->setDueDate('')
        // 'customfield_14362', implode("', '", $test['group'])) // have to implode any customfield that will use multiple values (strings)
        // Add custom Field mappings
        ->addCustomField('customfield_14364', $stories)
        ->addCustomField('customfield_14362', implode("', '", $test['group']))// have to implode any customfield that will use multiple values (strings)
                // TODO: group value doesnt match to anything in MC. Will have to ignore and find default value from single select dropdown (like 'severity' field below)
        ->addCustomField('customfield_12720', ['value' => $severity])// TODO: for any customfields taking LIST, need to ['value' => 'foo'] and [ ['value => 'foo'], ['value' => 'bar'] ] or multiple list selections
        ;
        return $issueField;
    }
}