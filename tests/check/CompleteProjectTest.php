<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

const COMPLETE_PROJECT_CHECK_TEST_PROJECT_PATH = __DIR__ . '/../explore/test-data/project';

/**
  * Register a complete-project check that reports one complete-project error.
  **/
function registerCompleteProjectErrorCheck(string $strCheckName): void {

  \ImmanentChecker\Check\register(\ImmanentChecker\STAGE_COMPLETE_PROJECT,
                                      $strCheckName,
                                      function () use ($strCheckName) {

                                        \ImmanentChecker\Error\completeProject($strCheckName,
                                                                                   'expected complete project check error',
                                                                                   array('Readme.md', 'src/Test.php'),
                                                                                   '{"context":"complete-project"}');

                                      });

}

/**
  * Return all complete-project errors reported by the given check.
  **/
function getCompleteProjectErrorsByCheck(string $strCheckName): array {

  $objRegistry = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\ERROR_COMPLETE_PROJECT);
  $arrErrors   = array();

  foreach($objRegistry->getAll() AS $objError)
    if($objError->get('check') === $strCheckName)
      $arrErrors[] = $objError;

  return $arrErrors;

}

/**
  * Expect complete-project checks to report one error for the explored project.
  **/
function testCompleteProjectCallsRegisteredCheckOnce() {

  $strCheckName = 'CompleteProjectCallsRegisteredCheckOnce';

  \ImmanentChecker\Explore\project(COMPLETE_PROJECT_CHECK_TEST_PROJECT_PATH);
  registerCompleteProjectErrorCheck($strCheckName);

  \ImmanentChecker\Check\completeProject();

  $arrErrors = getCompleteProjectErrorsByCheck($strCheckName);
  $arrError  = $arrErrors[0]->getAll();

  assertEquals(count($arrErrors), 1);
  assertEquals($arrError['message'], 'expected complete project check error');
  assertEquals($arrError['details'], '{"context":"complete-project"}');

}

/**
  * Expect complete-project checks to receive the complete project DataObjectPool.
  **/
function testCompleteProjectPassesCompleteProjectDataObjectPoolToCallback() {

  $strCheckName   = 'CompleteProjectPassesCompleteProjectDataObjectPoolToCallback';
  $strProjectPath = realpath(COMPLETE_PROJECT_CHECK_TEST_PROJECT_PATH);

  \ImmanentChecker\Explore\project(COMPLETE_PROJECT_CHECK_TEST_PROJECT_PATH);

  \ImmanentChecker\Check\register(\ImmanentChecker\STAGE_COMPLETE_PROJECT,
                                      $strCheckName,
                                      function (\ImmanentChecker\DataObjectPool $objProject) use ($strCheckName, $strProjectPath) {

                                        $objReadme = $objProject->get($strProjectPath . '/Readme.md');
                                        $objSource = $objProject->get($strProjectPath . '/src/Test.php');

                                        \ImmanentChecker\Error\completeProject($strCheckName,
                                                                                   'expected complete project check error',
                                                                                   array($objReadme->get('relative_path'),
                                                                                         $objSource->get('relative_path')));

                                      });

  \ImmanentChecker\Check\completeProject();

  $arrErrors = getCompleteProjectErrorsByCheck($strCheckName);
  $arrError  = $arrErrors[0]->getAll();

  assertEquals(count($arrErrors), 1);
  assertEquals($arrError['files'], array('Readme.md', 'src/Test.php'));

}

/**
  * Expect all() to execute registered complete-project checks.
  **/
function testAllCallsCompleteProjectChecks() {

  $strCheckName = 'AllCallsCompleteProjectChecks';

  \ImmanentChecker\Explore\project(COMPLETE_PROJECT_CHECK_TEST_PROJECT_PATH);
  registerCompleteProjectErrorCheck($strCheckName);

  \ImmanentChecker\Check\all();

  $arrErrors = getCompleteProjectErrorsByCheck($strCheckName);

  assertEquals(count($arrErrors), 1);

}
