<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

const PROJECT_CHECK_TEST_PROJECT_PATH = __DIR__ . '/../explore/test-data/project';

/**
  * Register a project check that reports one project error.
  **/
function registerProjectErrorCheck(string $strCheckName): void {

  \ImmanentChecker\Check\register(\ImmanentChecker\STAGE_PROJECT,
                                      $strCheckName,
                                      function () use ($strCheckName) {

                                        \ImmanentChecker\Error\project($strCheckName,
                                                                           'expected project check error',
                                                                           array('Readme.md', 'src/Test.php'),
                                                                           '{"context":"project"}');

                                      });

}

/**
  * Return all project errors reported by the given check.
  **/
function getProjectErrorsByCheck(string $strCheckName): array {

  $objRegistry = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\ERROR_PROJECT);
  $arrErrors   = array();

  foreach($objRegistry->getAll() AS $objError)
    if($objError->get('check') === $strCheckName)
      $arrErrors[] = $objError;

  return $arrErrors;

}

/**
  * Expect project checks to report one error for the explored project.
  **/
function testProjectCallsRegisteredCheckOnce() {

  $strCheckName = 'ProjectCallsRegisteredCheckOnce';

  \ImmanentChecker\Explore\project(PROJECT_CHECK_TEST_PROJECT_PATH);
  registerProjectErrorCheck($strCheckName);

  \ImmanentChecker\Check\project();

  $arrErrors = getProjectErrorsByCheck($strCheckName);
  $arrError  = $arrErrors[0]->getAll();

  assertEquals(count($arrErrors), 1);
  assertEquals($arrError['message'], 'expected project check error');
  assertEquals($arrError['details'], '{"context":"project"}');

}

/**
  * Expect project checks to receive the filtered project DataObjectPool.
  **/
function testProjectPassesProjectDataObjectPoolToCallback() {

  $strCheckName   = 'ProjectPassesProjectDataObjectPoolToCallback';
  $strProjectPath = realpath(PROJECT_CHECK_TEST_PROJECT_PATH);

  \ImmanentChecker\Explore\project(PROJECT_CHECK_TEST_PROJECT_PATH);

  \ImmanentChecker\Check\register(\ImmanentChecker\STAGE_PROJECT,
                                      $strCheckName,
                                      function (\ImmanentChecker\DataObjectPool $objProject) use ($strCheckName, $strProjectPath) {

                                        $objReadme = $objProject->get($strProjectPath . '/Readme.md');
                                        $objSource = $objProject->get($strProjectPath . '/src/Test.php');

                                        \ImmanentChecker\Error\project($strCheckName,
                                                                           'expected project check error',
                                                                           array($objReadme->get('relative_path'),
                                                                                 $objSource->get('relative_path')));

                                      });

  \ImmanentChecker\Check\project();

  $arrErrors = getProjectErrorsByCheck($strCheckName);
  $arrError  = $arrErrors[0]->getAll();

  assertEquals(count($arrErrors), 1);
  assertEquals($arrError['files'], array('Readme.md', 'src/Test.php'));

}

/**
  * Expect all() to execute registered project checks.
  **/
function testAllCallsProjectChecks() {

  $strCheckName = 'AllCallsProjectChecks';

  \ImmanentChecker\Explore\project(PROJECT_CHECK_TEST_PROJECT_PATH);
  registerProjectErrorCheck($strCheckName);

  \ImmanentChecker\Check\all();

  $arrErrors = getProjectErrorsByCheck($strCheckName);

  assertEquals(count($arrErrors), 1);

}
