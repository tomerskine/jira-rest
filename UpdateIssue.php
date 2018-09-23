<?php
/**
 * Created by PhpStorm.
 * User: terskine
 * Date: 7/11/18
 * Time: 10:03
 */

namespace Magento\JZI;

require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\JiraException;

class UpdateIssue {

    public $toUpdate;

    function __construct($toUpdate){
        $this->toUpdate = $toUpdate;
    }

    function updateIssueREST(){
        try{
        $issueField = new IssueField(true);

        $issueField->setAssigneeName("admin")
            ->setPriorityName("Blocker")
            ->setIssueType("Task")
            ->addLabel("test-label-first")
            ->addLabel("test-label-second")
            ->addVersion("1.0.1")
            ->addVersion("1.0.2")
            ->setDescription("This is a shorthand for a set operation on the summary field")
        ;

        // optionally set some query params
        $editParams = [
            'notifyUsers' => false,
        ];

        $issueService = new IssueService();

        // You can set the $paramArray param to disable notifications in example
        $ret = $issueService->update($issueKey, $issueField, $editParams);

        var_dump($ret);
    } catch (JiraException $e) {
        $this->assertTrue(FALSE, "update Failed : " . $e->getMessage());
        }
    }

    function updateSkippedTest($test) {
        //do skipped
    }

    function getErrors(){
        return 5;
    }

    // TODO : REMOVE
    static function updateDryRunIssuesREST($test)
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