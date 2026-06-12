<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

/**
  * Expect CLI arguments to be parsed into project paths, suite paths, and
  * exclude patterns.
  **/
function testParseArgumentsStoresExpectedOptions() {

  $strProjectPath = __DIR__ . '/../explore/test-data/project';

  $arrOptions = \ImmanentChecker\Cli\parseArguments(array('immanent-checker',
                                                              '--suite',
                                                              __DIR__,
                                                              '--exclude',
                                                              'vendor/*',
                                                              '--exclude',
                                                              'node_modules/*',
                                                              '--project',
                                                              $strProjectPath));

  assertEquals($arrOptions['suite'],   array(__DIR__));
  assertEquals($arrOptions['project'], array($strProjectPath));
  assertEquals($arrOptions['exclude'], array('vendor/*', 'node_modules/*'));

}

/**
  * Expect CLI parsing to allow multiple suites and projects for one run.
  **/
function testParseArgumentsAllowsMultipleSuitesAndProjects() {

  $strProjectPath = __DIR__ . '/../explore/test-data/project';

  $arrOptions = \ImmanentChecker\Cli\parseArguments(array('immanent-checker',
                                                              '--suite',
                                                              __DIR__,
                                                              '--suite',
                                                              __DIR__ . '/../check',
                                                              '--project',
                                                              $strProjectPath,
                                                              '--project',
                                                              $strProjectPath));

  assertEquals(count($arrOptions['suite']),   2);
  assertEquals(count($arrOptions['project']), 2);

}

/**
  * Expect CLI parsing to reject calls without a project path.
  **/
function testParseArgumentsRejectsMissingProjectPath() {

  $cloTest = function () {

    \ImmanentChecker\Cli\parseArguments(array('immanent-checker',
                                                  '--suite',
                                                  __DIR__));

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect CLI parsing to reject calls without a suite path.
  **/
function testParseArgumentsRejectsMissingSuitePath() {

  $cloTest = function () {

    \ImmanentChecker\Cli\parseArguments(array('immanent-checker',
                                                  '--project',
                                                  __DIR__ . '/../explore/test-data/project'));

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect CLI parsing to reject unknown options.
  **/
function testParseArgumentsRejectsUnknownOption() {

  $cloTest = function () {

    \ImmanentChecker\Cli\parseArguments(array('immanent-checker',
                                                  '--suite',
                                                  __DIR__,
                                                  '--unknown',
                                                  'value',
                                                  '--project',
                                                  __DIR__ . '/../explore/test-data/project'));

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect CLI parsing to reject options without values.
  **/
function testParseArgumentsRejectsMissingOptionValue() {

  $cloTest = function () {

    \ImmanentChecker\Cli\parseArguments(array('immanent-checker',
                                                  '--suite',
                                                  __DIR__,
                                                  '--project'));

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect CLI parsing to reject suite paths that are not directories.
  **/
function testParseArgumentsRejectsSuitePathThatIsNotDirectory() {

  $cloTest = function () {

    \ImmanentChecker\Cli\parseArguments(array('immanent-checker',
                                                  '--suite',
                                                  __FILE__,
                                                  '--project',
                                                  __DIR__ . '/../explore/test-data/project'));

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect CLI parsing to reject project paths that are not directories.
  **/
function testParseArgumentsRejectsProjectPathThatIsNotDirectory() {

  $cloTest = function () {

    \ImmanentChecker\Cli\parseArguments(array('immanent-checker',
                                                  '--suite',
                                                  __DIR__,
                                                  '--project',
                                                  __FILE__));

  };

  expectException($cloTest, "\Exception");

}
