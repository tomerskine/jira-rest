<?php

namespace Magento\JZI;

use Magento\JZI\LoggingUtil;
include ('CreateIssue.php');
include ('Util/LoggingUtil.php');


class CreateManager
{

    public static $createManager;

    public static function getInstance() {
        if (!self::$createManager) {
            self::$createManager = new CreateManager();
        }

        return self::$createManager;


    }

    public function performCreateOperations($createByName, $createById, $skipped) {
        //loop - test for if skipped. if skipped, pass to skipCreate, if not, vanillaCreate
        foreach ($createById as $id) {
            if (array_key_exists($id, $skipped)) {
                $this->createSkipped($id);
            }
            else {
                $createIssue = new CreateIssue($id);
                $reponse = $createIssue::createIssuesREST($id);
            }
        }
    }

    public function createSkipped($id) {
        $createIssue = new CreateIssue($id);
        $response = $createIssue::createSkippedTest($id); //TODO: create createSkippedTest method in CreateIssue class
        return $response;

    }

    //TODO : REMOVE
    public function performDryRunCreateOperations($createByName, $createById, $skipped) {
        //loop - test for if skipped. if skipped, pass to skipCreate, if not, vanillaCreate
        foreach ($createById as $id) {
            // if (array_key_exists($id, $skipped)) {
            if (isset($id['skip'])) {
                $this->createDryRunSkipped($id);
            }
            else {
                $createIssue = new CreateIssue($id);
                $response = $createIssue::createDryRunIssuesREST($id);
                $createdIssueById[] = $response;
                LoggingUtil::getInstance()->getLogger(CreateManager::class)->info('NEW TEST sent to CREATE: ' . $id['title'][0]);
            }
        }
        return $createdIssueById;
    }

    //TODO : REMOVE
    public function createDryRunSkipped($id) {
        $createIssue = new CreateIssue($id);
        LoggingUtil::getInstance()->getLogger(CreateManager::class)->info('SKIPPED TEST sent to CREATE: ' . $id['title'][0]);
        //$response = $createIssue::createDryRunSkippedTest($id); //TODO: create createSkippedTest method in CreateIssue class
        //return $response;
        }


}