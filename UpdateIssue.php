<?php
/**
 * Created by PhpStorm.
 * User: terskine
 * Date: 7/11/18
 * Time: 10:03
 */

namespace Magento\JZI;

require_once ('../../autoload.php');

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\JiraException;
use JiraRestApi\Issue\Transition;
use JiraRestApi\IssueLink\IssueLink;
use JiraRestApi\IssueLink\IssueLinkService;

include_once ('Util/LoggingUtil.php');

class UpdateIssue {

    public $Update;

    function __construct($Update){
        $this->toUpdate = $Update;
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


    static function updateRequireTransitionFields($update) {
        $issueField = new IssueField();
        $issueField->setIssueType("Test");
        $issueField->setProjectKey("MC");
        $issueField->environment = "None";
        $issueField->resolution = "Done";
        $issueService = new IssueService();

        // You can set the $paramArray param to disable notifications in example
        $ret = $issueService->update($update['key'], $issueField);
        print_r('Updating reqd fields');
        return $ret;
    }
    // TODO : REMOVE
    static function updateDryRunIssuesREST($update, $key)
    {
        $update += ['key' => $key];
        $issueField = self::buildUpdateIssueField($update);
        LoggingUtil::getInstance()->getLogger(UpdateIssue::class)->info('TEST sent to UPDATE : ' . $update['key']);
        // TODO : Add call to REAL update issue REST call
        if (isset($update['skip'])) {
            self::skipTestStatusTransition($update);
            self::skipTestLinkIssue($update);
        }
        $issueField->setIssueType("Test");
        $issueField->setProjectKey("MC");
        $issueService = new IssueService();

        // You can set the $paramArray param to disable notifications in example
        $ret = $issueService->update($update['key'], $issueField);
        return $ret;
    }

    public static function buildUpdateIssueField($update) {
        $issueField = new IssueField();
        if (isset($update['title'])) {
            $issueField->setSummary($update['title']);
        }
        if (isset($update['description'])) {
            $issueField->setDescription($update['description']);
        }
        if (isset($update['stories'])) {
            $issueField->addCustomField('customfield_14364', $update['stories']);
        }
        if (isset($update['severity'])) {
            $issueField->addCustomField('customfield_12720', ['value' => $update['severity']]);
            //$issueField->addCustomField('customfield_12720', ['value' => $test['severity'][0]])
        }
        return $issueField;
    }

    public static function skipTestStatusTransition($update) {
        $issueKey = $update['key'];
        try {
            $transition = new Transition();
            $transition->setTransitionName('Skipped');
            $transition->setCommentBody('MFTF INTEGRATION - Setting SKIPPED status.');

            $skipTransitionIssueService = new IssueService();

            $skipTransitionIssueService->transition($issueKey, $transition);
            //if ($skipTransitionIssueService->http_response == 204) {
//                print_r("\n" . "SUCCESS! " . $issueKey . " set to status SKIPPED");
//            }
        } catch (JiraException $e) {
            //$this->assertTrue(FALSE, "add Comment Failed : " . $e->getMessage());
        }
    }

    public function skipTestLinkIssue($update) {
        try {
            $il = new IssueLink();

            $il->setInwardIssue($update['skip'])
                ->setOutwardIssue($update['key'])
                ->setLinkTypeName('Blocks' )
                ->setComment('Blocking issue for Skipped test');

            $ils = new IssueLinkService();

            $ret = $ils->addIssueLink($il);

        } catch (JiraException $e) {
            print("Error Occured! " . $e->getMessage());
        }
    }
}