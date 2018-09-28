<?php

namespace Magento\JZI;

use Magento\JZI\LoggingUtil;
include_once ('CreateIssue.php');
include_once ('ZephyrComparison.php');
include_once ('UpdateIssue.php');
include_once ('Util/LoggingUtil.php');


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
    public function performDryRunCreateOperations($createByName) {
        //loop - test for if skipped. if skipped, pass to skipCreate, if not, vanillaCreate
        foreach ($createByName as $id) {
            // if (array_key_exists($id, $skipped)) {
            $createIssue = new CreateIssue($id);
            $response = $createIssue::createDryRunIssuesREST($id);
            $createdIssueByName[] = $response;
            $mftfLoggingDescriptor = ZephyrComparison::mftfLoggingDescriptor($id);
            //LoggingUtil::getInstance()->getLogger(CreateManager::class)->info('NEW TEST sent to CREATE: ' . $mftfLoggingDescriptor);

            if (isset($id['skip'])) {
                $id += ['key' => $reponse];
                UpdateIssue::skipTestLinkIssue($id);
            }
        }
        return $createdIssueByName;
    }

    //TODO : REMOVE
    public function createDryRunSkipped($id) {
        $createIssue = new CreateIssue($id);
        LoggingUtil::getInstance()->getLogger(CreateManager::class)->info('SKIPPED TEST sent to CREATE: ' . $id['title'][0]);
        //$response = $createIssue::createDryRunSkippedTest($id); //TODO: create createSkippedTest method in CreateIssue class
        //return $response;
        }


}