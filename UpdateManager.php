<?php
/**
 * Created by PhpStorm.
 * User: terskine
 * Date: 9/16/18
 * Time: 8:12 PM
 */

namespace Magento\JZI;

include_once ('UpdateIssue.php');
include_once ('Util/LoggingUtil.php');

class UpdateManager
{
    public static $updateManager;

    public static function getInstance() {
        if (!self::$updateManager) {
            self::$updateManager = new UpdateManager();
        }

        return self::$updateManager;
    }

    public function performUpdateOperations($updates, $skipped) {
        //loop - test for if skipped. if skipped, pass to skipCreate, if not, vanillaCreate
        foreach ($updates as $id) {
            if (array_key_exists($id, $skipped)) {
                $this->updateSkipped($id);
            }
            else {
                $updateIssue = new UpdateIssue($id);
                $reponse = $updateIssue::updateIssueREST($id);
            }
        }
    }

    public function updateSkipped($id) {
        $updateIssue = new UpdateIssue($id);
        $response = $updateIssue::updateSkippedTest($id);
        return $response;

    }

    //TODO : REMOVE
    public function performDryRunUpdateOperations($updates) {
        //loop - test for if skipped. if skipped, pass to skipCreate, if not, vanillaCreate
        foreach ($updates as $key => $update) {
            // if (array_key_exists($id, $skipped)) {
//            if (isset($update['skip'])) {
//                $this->updateDryRunSkipped($update, $key);
//            }
//            else {
                $updateIssue = new UpdateIssue($update);
                $response = $updateIssue::updateDryRunIssuesREST($update, $key);
                //$updateIssue[] = $response;
                LoggingUtil::getInstance()->getLogger(UpdateManager::class)->info('TEST sent to UPDATE: ' . $key);
//            }
        }
    }

    //TODO : REMOVE
    public function updateDryRunSkipped($id, $key) {
        //$updateIssue = new UpdateIssue($id);
        LoggingUtil::getInstance()->getLogger(UpdateManager::class)->info('SKIPPED TEST sent to UPDATE: ' . $key);
        //$response = $createIssue::createDryRunSkippedTest($id); //TODO: create createSkippedTest method in CreateIssue class
        //return $response;
    }

}