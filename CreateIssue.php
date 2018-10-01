<?php

namespace Magento\JZI;

//require 'vendor/autoload.php';

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\JiraException;

include_once ('TransitionIssue.php');

class createIssue
{

    public $test;

    function __construct($id)
    {
        $this->test = $this->defaultMissingFields($id);
    }

    static function defaultMissingFields($test)
    {
        if (!(isset($test['stories']))) {
            $test['stories'][0] = 'NO STORY';
        }
        if (!(isset($test['severity']))) {
            $test['severity'][0] = '4-Minor';
        }
        if (!(isset($test['title']))) {
            $test['title'][0] = 'NO TITLE';
        }
        if (!(isset($test['description']))) {
            $test['description'][0] = 'NO DESCRIPTION';
        }
        return $test;
    }

    function templatecreateIssuesREST($test)
    {
        //need to make multiple payloads for mass create issue
        //need each test as separate array of tuples
        try {
            $issueField = new IssueField();

            $issueField->setProjectKey($test['Project'])//TODO: No project in MFTF array
            ->setSummary($test['title'][0])// Use Title from MFTF array
            ->setAssigneeName($test['AssigneeName'])// TODO: choose assignee
            ->setIssueType('Test')// OK
            ->setDescription($test['description'][0])// OK
            // ->addVersion($test['version']) // version?
            // ->addComponents(['', '']) // MFTF does not record Components -- TODO: is component used for reporting?
            // set issue security if you need.
            //->setSecurityId(10001 /* security scheme id */)
            //->setDueDate('')
            // 'customfield_14362', implode("', '", $test['group'])) // have to implode any customfield that will use multiple values (strings)
            // Add custom Field mappings
            ->addCustomField('customfield_14364', $test['stories'][0])
                ->addCustomField('customfield_14362', implode("', '", $test['group'][0]))// have to implode any customfield that will use multiple values (strings)
                ->addCustomField('customfield_12720', ['value' => $test['severity'][0]])// TODO: for any customfields taking LIST, need to ['value' => 'foo'] and [ ['value => 'foo'], ['value' => 'bar'] ] or multiple list selections
            ;

            $issueService = new IssueService();

            $ret = $issueService->create($issueField);
            //$ret = $issueService->createMultiple([$issueFieldOne, $issueFieldTwo]);

            //If success, Returns a link to the created issue.
            return $ret;
        } catch (JiraException $e) {
            print("Error Occured! " . $e->getMessage());
        }

    }

    static function createIssuesREST($test)
    {
        $test = self::defaultMissingFields($test);
        $issueField = new IssueField();

        $issueField->setProjectKey('MC')//TODO: No project in MFTF array
        ->setSummary($test['title'][0])// Use Title from MFTF array
        ->setAssigneeName('terskine')// TODO: choose assignee
        ->setIssueType('Test')// OK
        ->setDescription($test['description'][0])// OK
        //->addVersion('2.3.0') // version?
        ->addComponents('Module/ Catalog')// MFTF does not record Components -- TODO: is component used for reporting?
        ->addCustomField('customfield_14364', $test['stories'][0])
            ->addCustomField('customfield_14362', ['value' => 'Catalog'])// have to implode any customfield that will use multiple values (strings)
            // TODO: group value doesnt match to anything in MC. Will have to ignore and find default value from single select dropdown (like 'severity' field below)
            ->addCustomField('customfield_12720', ['value' => $test['severity'][0]])// TODO: for any customfields taking LIST, need to ['value' => 'foo'] and [ ['value => 'foo'], ['value' => 'bar'] ] or multiple list selections
            ->addCustomField('customfield_13324', ['value' => 'MFTF Test']);
        //$issueField->fixVersions = [['id'=>'18972']];
        $issueField->fixVersions = [['name' => '2.3.0']];

        $issueService = new IssueService();
        $ret = $issueService->create($issueField);
        LoggingUtil::getInstance()->getLogger(CreateIssue::class)->info
        ("CREATING REAL REST : " . $issueField->summary . " " . $issueField->description);
        //LoggingUtil::getInstance()->getLogger(CreateIssue::class)->info("CREATED ISSUE: " . $ret->key);

        // transition this newly created issue to AUTOMATED. Newly created status = "Open"
        $status = "Open";
        TransitionIssue::statusTransitionToAutomated($ret->key, $status);
        //LoggingUtil::getInstance()->getLogger(CreateIssue::class)->info("NEW ISSUE SET TO AUTOMATED: " . $ret-key);
        return $ret->key;
    }

    public function createM2Migration($zephyrTest) {
        $test = [];
        if (isset($zephyrTest['description'])) {
            $test['description'][0] = $zephyrTest['description'];
        }
        else {
            $test['description'][0] = "NO DESCRIPTION";
        }

        if (isset($zephyrTest['summary'])) {
            $test['title'][0] = $zephyrTest['summary'];
        }
        else {
            $test['title'][0] = "NO TITLE";
        }

        if (isset($zephyrTest['customfield_12720'])) {
            $test['severity'][0] = $zephyrTest['customfield_12720']['value'];
        }
        else {
            $test['severity'] = '4-Minor';
        }


//        if (isset($mftfTest['stories']) && isset($zephyrTest['customfield_14364'])) {
//            if (!($mftfTest['stories'][0] == $zephyrTest['customfield_14364'])) {
//                $this->mismatches[$key]['stories'] = $mftfTest['stories'][0];
//            }
//        }
//        elseif (isset($mftfTest['stories'])) {
//            $this->mismatches[$key]['stories'] = $mftfTest['stories'][0];
//        }
//
//        if ((isset($mftfTest['skip'])) && (!($zephyrTest['status']['name'] == "Skipped"))) {
//            $this->mismatches[$key]['skip'] = $mftfTest['skip'][0]; // TODO : do we need to handle multiple skip associated Ids?
//        }
//
//
//        if (isset($this->mismatches[$key])) {
//            $this->mismatches[$key]['status'] = $zephyrTest['status']['name'];
//        }



        $test = self::defaultMissingFields($test);
        $issueField = new IssueField();

        $issueField->setProjectKey('MC')//TODO: No project in MFTF array
        ->setSummary($test['title'][0])// Use Title from MFTF array
        ->setAssigneeName('terskine')// TODO: choose assignee
        ->setIssueType('Test')// OK
        ->setDescription($test['description'][0])// OK
        //->addVersion('2.3.0') // version?
        ->addComponents('Module/ Catalog')// MFTF does not record Components -- TODO: is component used for reporting?
        ->addCustomField('customfield_14364', $test['stories'][0])
            ->addCustomField('customfield_14362', ['value' => 'Catalog'])// have to implode any customfield that will use multiple values (strings)
            // TODO: group value doesnt match to anything in MC. Will have to ignore and find default value from single select dropdown (like 'severity' field below)
            ->addCustomField('customfield_12720', ['value' => $test['severity'][0]])// TODO: for any customfields taking LIST, need to ['value' => 'foo'] and [ ['value => 'foo'], ['value' => 'bar'] ] or multiple list selections
            ->addCustomField('customfield_13324', ['value' => 'MFTF Test']);
        //$issueField->fixVersions = [['id'=>'18972']];
        $issueField->fixVersions = [['name' => '2.3.0']];

        $issueService = new IssueService();
        $ret = $issueService->create($issueField);
        //LoggingUtil::getInstance()->getLogger(CreateIssue::class)->info("CREATED ISSUE: " . $ret->key);

        // transition this newly created issue to AUTOMATED. Newly created status = "Open"
        $status = "Open";
        TransitionIssue::statusTransitionToAutomated($ret->key, $status);
        //LoggingUtil::getInstance()->getLogger(CreateIssue::class)->info("NEW ISSUE SET TO AUTOMATED: " . $ret-key);
        return $ret->key;
    }

    // TODO : REMOVE
    static function createDryRunIssuesREST($test)
    {
        $test = self::defaultMissingFields($test);

        $issueField = new IssueField();

        $issueField->setProjectKey('MC')//TODO: No project in MFTF array
        ->setSummary($test['title'][0])// Use Title from MFTF array
        ->setAssigneeName('terskine')// TODO: choose assignee
        ->setIssueType('Test')// OK
        ->setDescription($test['description'][0])// OK
        //->addVersion('2.3.0') // version?
        ->addComponents('Module/ Catalog')// MFTF does not record Components -- TODO: is component used for reporting?
        ->addCustomField('customfield_14364', $test['stories'][0])
            ->addCustomField('customfield_14362', ['value' => 'Catalog'])// have to implode any customfield that will use multiple values (strings)
            // TODO: group value doesnt match to anything in MC. Will have to ignore and find default value from single select dropdown (like 'severity' field below)
            ->addCustomField('customfield_12720', ['value' => $test['severity'][0]])// TODO: for any customfields taking LIST, need to ['value' => 'foo'] and [ ['value => 'foo'], ['value' => 'bar'] ] or multiple list selections
            ->addCustomField('customfield_13324', ['value' => 'MFTF Test']);
        //$issueField->fixVersions = [['id'=>'18972']];
        $issueField->fixVersions = [['name' => '2.3.0']];

        $issueService = new IssueService();
        //$ret = $issueService->create($issueField);
        LoggingUtil::getInstance()->getLogger(CreateIssue::class)->info
        ("DRY RUN : " . $issueField->summary . " " . $issueField->description);
        //LoggingUtil::getInstance()->getLogger(CreateIssue::class)->info("CREATED ISSUE: " . $ret->key);

        // transition this newly created issue to AUTOMATED. Newly created status = "Open"
        $status = "Open";
        //TransitionIssue::statusTransitionToAutomated($ret->key, $status);
        //LoggingUtil::getInstance()->getLogger(CreateIssue::class)->info("NEW ISSUE SET TO AUTOMATED: " . $ret-key);
//        $test += ['key' => $ret->key];
        if (isset(test['skip'])) {
            //updateIssue::skipTestStatusTransition($update);
            //updateIssue::skipTestLinkIssue($update);
            LoggingUtil::getInstance()->getLogger(CreateIssue::class)->info("NEW TEST SET SKIPPED");
            //return $ret->key;
        }
    }
}
