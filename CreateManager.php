<?php

namespace Magento\JZI;


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

}