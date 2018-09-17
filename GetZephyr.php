<?php

//require 'vendor/autoload.php';
//namespace Magento\JZI;

require(__DIR__ . "/../../../vendor/autoload.php");

use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

class GetZephyr {

    public $zephyrID;

//	function __construct($zephyrID){
//	    $this->zephyrID = $zephyrID;
//    }
    public function object_to_array_recursive ( $object, $assoc=1, $empty='' )
    {
        $out_arr = array();
        $assoc = (!empty($assoc)) ? TRUE : FALSE;

        if (!empty($object)) {

            $arrObj = is_object($object) ? get_object_vars($object) : $object;

            $i=0;
            foreach ($arrObj as $key => $val) {
                $akey = ($assoc !== FALSE) ? $key : $i;
                if (is_array($val) || is_object($val)) {
                    $out_arr[$key] = (empty($val)) ? $empty : $this->object_to_array_recursive($val);
                }
                else {
                    $out_arr[$key] = (empty($val)) ? $empty : (string)$val;
                }
                $i++;
            }

        }

        return $out_arr;
    }

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
			print("Error Occurred! " . $e->getMessage());
		}
        return json_decode($issue);
	}

	public function getIssuesByProject($project) {
        //TODO: Send JQL query and parse results
        //$jql = 'project = '. $project. ' and issueType = Test and status Automated';
        $jql = 'project = MC AND issueType = Test AND status = Automated AND reporter = treece'; //hardcoded jql to get smaller subset of TESTS
        $zephyrIDs =[];

        try {
            $query = new IssueService();

            $ret = $query->search($jql);
            //$data = json_decode(json_encode($ret), false); //= json_decode(json_encode($response->response->docs), true);
            //$data = json_decode(json_encode($ret), false); //= json_decode(json_encode($response->response->docs), true);
            //var_dump(get_object_vars($ret));
            $data = $this->object_to_array_recursive($ret, FALSE);
            //print_r($data["issues"][0]);
            foreach ($data['issues'] as $k) {
                $zephyrIDs[$k['key']] = $k['fields']; // creates array of [1001 : MC-01, 1002 : MC-02]
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
            $zephyrTests[$id] = $this->getZephyrById($id);  // Returns array of [MC-01 : [arrayOfFields]
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
//$getZephyr = new GetZephyr();
//$zephyrIds = $getZephyr->getIssuesByProject('tom');
//$zephyrIds = array_slice($zephyrIds,0,5);
//print_r($zephyrIds);