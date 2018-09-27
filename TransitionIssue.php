<?php
/**
 * Created by PhpStorm.
 * User: terskine
 * Date: 9/26/18
 * Time: 9:42 PM
 */

namespace Magento\JZI;

require_once ('../../autoload.php');

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\JiraException;
use JiraRestApi\Issue\Transition;
use Magento\JZI\UpdateIssue;
include ('UpdateIssue.php');
include ('../../lesstif/php-jira-rest-client/src/Issue/Transition.php');
include ('../../lesstif/php-jira-rest-client/src/Issue/IssueService.php');

class transitionIssue
{

    public static function statusTransitionToAutomated($update) {
        $update = ['key' => 'MC-4232', 'status'=>'Review Passed'];
        $issueKey = $update['key'];
        $startingStatus = $update['status'];

        $projectMcTransitionStates = ["Open", "In Progress", "Ready for Review", "In Review", "Review Passed", "Automated"]; //List of all transitions from Open to Automated. Skip transition handled separately.
        $currentStatusOffset = array_search($startingStatus, $projectMcTransitionStates);
        if ($startingStatus == "Skipped") {
            unset($projectMcTransitionStates[6]);
        }
        $requiredTransitons = array_slice($projectMcTransitionStates, $currentStatusOffset+1);

        foreach ($requiredTransitons as $status) {
            try {
                $transition = new Transition();
                if ($status == "Automated") {
                    //UpdateIssue::updateRequireTransitionFields($update);
                    // 'customfield_13783' => ['value' =>'None'],
                    $transition->fields = ['resolution' => ['name' => 'Done'], 'customfield_13783' => ['value' =>'Unknown']];
                }
                $transition->setTransitionName($status);
                $transition->setCommentBody("MFTF INTEGRATION - Setting " . $status . " status.");

                $skipTransitionIssueService = new IssueService();

                $skipTransitionIssueService->transition($issueKey, $transition);
//                if ($skipTransitionIssueService->http_response == 204) {
//                print_r("\n" . "SUCCESS! " . $issueKey . " set to status "  . $status);
//            }
            } catch (JiraException $e) {
                //$this->assertTrue(FALSE, "add Comment Failed : " . $e->getMessage());
            }
        }

    }

}

$empty = [];
TransitionIssue::statusTransitionToAutomated($empty);
