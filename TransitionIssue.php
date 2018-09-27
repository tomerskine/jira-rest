<?php
/**
 * Created by PhpStorm.
 * User: terskine
 * Date: 9/26/18
 * Time: 9:42 PM
 */

namespace Magento\JZI;

//use JiraRestApi\Issue\IssueService;
//use JiraRestApi\Issue\IssueField;
//use JiraRestApi\JiraException;
//use JiraRestApi\Issue\Transition;
include ('../../lesstif/php-jira-rest-client/src/Issue/Transition.php');
include ('../../lesstif/php-jira-rest-client/src/Issue/IssueService.php');

class transitionIssue
{

    public static function statusTransition($update) {
        $update = ['key' => 'MC-4229', 'status'=>'Open'];
        $issueKey = $update['key'];
        $startingStatus = $update['status'];

        $projectMcTransitionStates = ["Open", "In Progress", "Ready For Review", "In Review", "Review Passed", "Automated", "Skipped"];
        $currentStatusOffset = array_search($startingStatus, $projectMcTransitionStates);
        $requiredTransitons = array_slice($projectMcTransitionStates, $currentStatusOffset);

        foreach ($requiredTransitons as $status) {
            try {
                $transition = new Transition();
                $transition->setTransitionName($status);
                $transition->setCommentBody("MFTF INTEGRATION - Setting " . $status . " status.");

                $skipTransitionIssueService = new IssueService();

                $skipTransitionIssueService->transition($issueKey, $transition);
                if ($skipTransitionIssueService->http_response == 204) {
                print_r("\n" . "SUCCESS! " . $issueKey . " set to status "  . $status);
            }
            } catch (JiraException $e) {
                //$this->assertTrue(FALSE, "add Comment Failed : " . $e->getMessage());
            }
        }

    }

}

$empty = [];
TransitionIssue::statusTransition($empty);
