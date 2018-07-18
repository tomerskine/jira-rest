<?php

//require 'vendor/autoload.php';
require(__DIR__ . "/../../../vendor/autoload.php");

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;


class GetZephyr{

    public $zephyrID;

//	function __construct($zephyrID){
//	    $this->zephyrID = $zephyrID;
//    }

    function getZephyrById($zephyrID){
        $issue = [];
		try {
		    $issueService = new IssueService();
			
		    $queryParam = [
		        'fields' => [  // default: '*all'
		            'summary',
		            'comment',
		        ],
		        'expand' => [
		            'renderedFields',
		            'names',
		            'schema',
		            'transitions',
		            'operations',
		            'editmeta',
		            'changelog',
		        ]
		    ];
		            
		    $issue = $issueService->get($zephyrID, $queryParam);
			
		    //var_dump($issue->fields);

		} catch (JiraException $e) {
			print("Error Occured! " . $e->getMessage());
		}
        return json_decode($issue);
	}

	function getIssuesByProject($project) {
        //TODO: Send JQL query and parse results
        $jql = 'project = project  and issueType = Test and status Automated';
        $zephyrIDs =[];

        try {
            $query = new IssueService();

            $ret = $query->search($jql);
            $data = json_decode($ret);
            foreach ($data['issues'] as $k) {
                $zephyrIDs[$k['key']] = $k['id']; // creates array of [1001 : MC-01, 1002 : MC-02]
            }
            if (isset($zephyrIDs)) {
                return $zephyrIDs;
            }
        } catch (JiraException $e) {
            //$this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
        }
        return $zephyrIDs;
    }

    function getAllZephyrTests($ids) {
        $zephyrTests =[];
        foreach ($ids as $id) {
            //$zephyrTests[] = '$id' => $this->getZephyrById($id); // ?
            //$zephyrTests[$id] = $this->getZephyrById($id);
            $zephyrTests[$id] = $this->getZephyrById($id);  // Returns array of [MC-01 : [name : 'something, description : 'something else', key : 'MC-01', id : '1001']
        }
        return $zephyrTests;

    }

    function prototypeGetIssuesByProject() {
        return null;

    }

    function prototypeGetAllZephyrTests() {
            $zephyrTests = ["features" => "prototypeFeature", "stories" => "prototypeStory", "title" => "prototypeTitle", "description" => "prototypeDescription", "testCaseId" => "TOM-123"];
            return $zephyrTests;
    }
}