<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

const RUN_TEST_PROJECT_PATH = __DIR__ . '/../explore/test-data/project';
const RUN_TEST_SUITE_PATH   = __DIR__ . '/test-data/suite';

/**
  * Expect a project run to load suites, explore the project, and execute checks.
  **/
function testRunProjectLoadsSuiteExploresProjectAndExecutesChecks() {

  \ImmanentCodeChecker\Run\project(RUN_TEST_PROJECT_PATH,
                                   array(RUN_TEST_SUITE_PATH),
                                   array('Readme.md'));

  $objErrors = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\ERROR_PROJECT);
  $arrErrors = $objErrors->getAll();

  assertEquals(count($arrErrors), 1);

  $objError = array_shift($arrErrors);
  $arrError = $objError->getAll();

  assertEquals($arrError['check'],   'RunProjectTestProjectCheck');
  assertEquals($arrError['message'], 'expected run project error');
  assertEquals(count($arrError['files']), 2);

}

/**
  * Expect a project run to reject suite directories without register.php.
  **/
function testRunProjectRejectsSuiteWithoutRegisterFile() {

  $cloTest = function () {

    \ImmanentCodeChecker\Run\project(RUN_TEST_PROJECT_PATH,
                                     array(__DIR__),
                                     array());

  };

  expectException($cloTest, "\Exception");

}
