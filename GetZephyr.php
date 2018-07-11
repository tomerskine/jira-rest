<?php

require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

class GetZephyr{

    public $zephyrID;

//	function __construct($zephyrID){
//	    $this->zephyrID = $zephyrID;
//    }

    function getZephyrById($zephyrID){
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
			
		    var_dump($issue->fields);	
		} catch (JiraException $e) {
			print("Error Occured! " . $e->getMessage());
		}
	}
}