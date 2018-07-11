<?php
/**
 * Created by PhpStorm.
 * User: terskine
 * Date: 7/11/18
 * Time: 10:03
 */

require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\JiraException;

class UpdateIssue {

    public $toUpdate;

    function __construct($toUpdate){
        $this->toUpdate = $toUpdate;
    }

    function updateIssue(){
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

    function getErrors(){
        return 5;
    }
}