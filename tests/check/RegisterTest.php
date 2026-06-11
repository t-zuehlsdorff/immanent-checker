<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

/**
  * Expect a registered check to be stored in the DataObjectPool of the selected
  * stage with its name, description, and callback unchanged.
  **/
function testRegisterStoresCheckInStageRegistry() {

  $strStage       = \ImmanentCodeChecker\STAGE_COMPLETE_PROJECT;
  $strName        = 'RegisterCompleteProject' . uniqid();
  $strDescription = 'test check registration';
  $cloCallback    = function () { return true; };

  \ImmanentCodeChecker\Check\register($strStage, $strName, $cloCallback, $strDescription);

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool($strStage);
  $objCheck    = $objRegistry->get($strName);
  $arrCheck    = $objCheck->getAll();

  assertEquals($arrCheck['name'],        $strName);
  assertEquals($arrCheck['description'], $strDescription);
  assertEquals($arrCheck['callback'],    $cloCallback);

}

/**
  * Expect the optional description argument to default to an empty string when
  * no description is provided.
  **/
function testRegisterUsesEmptyDescriptionByDefault() {

  $strStage    = \ImmanentCodeChecker\STAGE_PROJECT;
  $strName     = 'RegisterWithoutDescription' . uniqid();
  $cloCallback = function () { return true; };

  \ImmanentCodeChecker\Check\register($strStage, $strName, $cloCallback);

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool($strStage);
  $arrCheck    = $objRegistry->get($strName)->getAll();

  assertEquals($arrCheck['description'], '');

}

/**
  * Expect registration to accept every known analysis stage.
  **/
function testRegisterAcceptsAllKnownStages() {

  $arrStages = array(\ImmanentCodeChecker\STAGE_COMPLETE_PROJECT,
                     \ImmanentCodeChecker\STAGE_PROJECT,
                     \ImmanentCodeChecker\STAGE_DIRECTORY,
                     \ImmanentCodeChecker\STAGE_FILE);

  foreach($arrStages AS $strStage) {

    $strName     = 'RegisterKnownStage' . uniqid();
    $cloCallback = function () { return true; };

    \ImmanentCodeChecker\Check\register($strStage, $strName, $cloCallback);

    $objRegistry = new \ImmanentCodeChecker\DataObjectPool($strStage);

    assertTrue($objRegistry->exists($strName));

  }

}

/**
  * Expect registration to reject stages that are not part of the known analysis
  * stages.
  **/
function testRegisterRejectsUnknownStage() {

  $cloTest = function () {

    \ImmanentCodeChecker\Check\register('unknown-stage',
                                        'RegisterUnknownStage',
                                        function () { return true; });

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect registration to reject a second check with the same name in the same
  * analysis stage.
  **/
function testRegisterRejectsDuplicateNameWithinStage() {

  $strStage    = \ImmanentCodeChecker\STAGE_FILE;
  $strName     = 'RegisterDuplicateName' . uniqid();
  $cloCallback = function () { return true; };

  \ImmanentCodeChecker\Check\register($strStage, $strName, $cloCallback);

  $cloTest = function () use ($strStage, $strName) {

    \ImmanentCodeChecker\Check\register($strStage, $strName, function () { return true; });

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect equal check names to be allowed when they are registered in different
  * analysis stages.
  **/
function testRegisterAllowsSameNameInDifferentStages() {

  $strName     = 'RegisterSameNameDifferentStages' . uniqid();
  $cloCallback = function () { return true; };

  \ImmanentCodeChecker\Check\register(\ImmanentCodeChecker\STAGE_DIRECTORY,
                                      $strName,
                                      $cloCallback);

  \ImmanentCodeChecker\Check\register(\ImmanentCodeChecker\STAGE_FILE,
                                      $strName,
                                      $cloCallback);

  $objDirectoryRegistry = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\STAGE_DIRECTORY);
  $objFileRegistry      = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\STAGE_FILE);

  assertTrue($objDirectoryRegistry->exists($strName));
  assertTrue($objFileRegistry->exists($strName));

}

/**
  * Expect the stage registry validator to reject empty check names.
  **/
function testStageRegistryRejectsEmptyCheckName() {

  $cloTest = function () {

    \ImmanentCodeChecker\Check\register(\ImmanentCodeChecker\STAGE_FILE,
                                        '',
                                        function () { return true; },
                                        'valid description');

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect the stage registry validator to reject check names with surrounding
  * whitespace.
  **/
function testStageRegistryRejectsCheckNameWithSurroundingWhitespace() {

  $cloTest = function () {

    \ImmanentCodeChecker\Check\register(\ImmanentCodeChecker\STAGE_FILE,
                                        ' InvalidCheckName ',
                                        function () { return true; },
                                        'valid description');

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect the stage registry validator to reject descriptions with surrounding
  * whitespace.
  **/
function testStageRegistryRejectsDescriptionWithSurroundingWhitespace() {

  $cloTest = function () {

    \ImmanentCodeChecker\Check\register(\ImmanentCodeChecker\STAGE_FILE,
                                        'InvalidDescriptionWhitespace' . uniqid(),
                                        function () { return true; },
                                        ' valid description ');

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect the default pattern to be '*' when no pattern is provided.
  **/
function testRegisterUsesDefaultPatternWhenNoneGiven() {

  $strStage    = \ImmanentCodeChecker\STAGE_FILE;
  $strName     = 'RegisterUsesDefaultPattern' . uniqid();
  $cloCallback = function () { return true; };

  \ImmanentCodeChecker\Check\register($strStage, $strName, $cloCallback);

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool($strStage);
  $arrCheck    = $objRegistry->get($strName)->getAll();

  assertEquals($arrCheck['pattern'], '*');

}

/**
  * Expect an explicit pattern to be stored in the check registration.
  **/
function testRegisterStoresExplicitPattern() {

  $strStage    = \ImmanentCodeChecker\STAGE_FILE;
  $strName     = 'RegisterStoresExplicitPattern' . uniqid();
  $cloCallback = function () { return true; };

  \ImmanentCodeChecker\Check\register($strStage, $strName, $cloCallback, '', '*.php');

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool($strStage);
  $arrCheck    = $objRegistry->get($strName)->getAll();

  assertEquals($arrCheck['pattern'], '*.php');

}

/**
  * Expect patterns to be accepted for directory checks.
  **/
function testRegisterAcceptsPatternForDirectoryStage() {

  $strStage    = \ImmanentCodeChecker\STAGE_DIRECTORY;
  $strName     = 'RegisterAcceptsPatternForDirectory' . uniqid();
  $cloCallback = function () { return true; };

  \ImmanentCodeChecker\Check\register($strStage, $strName, $cloCallback, '', 'src/*');

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool($strStage);
  $arrCheck    = $objRegistry->get($strName)->getAll();

  assertEquals($arrCheck['pattern'], 'src/*');

}

/**
  * Expect registration to reject a non-default pattern for the complete project
  * stage.
  **/
function testRegisterRejectsPatternForCompleteProjectStage() {

  $cloTest = function () {

    \ImmanentCodeChecker\Check\register(\ImmanentCodeChecker\STAGE_COMPLETE_PROJECT,
                                        'RegisterRejectsPatternForCompleteProject' . uniqid(),
                                        function () { return true; },
                                        '',
                                        '*.php');

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect registration to reject a non-default pattern for the project stage.
  **/
function testRegisterRejectsPatternForProjectStage() {

  $cloTest = function () {

    \ImmanentCodeChecker\Check\register(\ImmanentCodeChecker\STAGE_PROJECT,
                                        'RegisterRejectsPatternForProject' . uniqid(),
                                        function () { return true; },
                                        '',
                                        '*.php');

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect the stage registry validator to reject empty patterns.
  **/
function testStageRegistryRejectsEmptyPattern() {

  $cloTest = function () {

    \ImmanentCodeChecker\Check\register(\ImmanentCodeChecker\STAGE_FILE,
                                        'RegisterRejectsEmptyPattern' . uniqid(),
                                        function () { return true; },
                                        '',
                                        '');

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect the stage registry validator to reject patterns with surrounding
  * whitespace.
  **/
function testStageRegistryRejectsPatternWithSurroundingWhitespace() {

  $cloTest = function () {

    \ImmanentCodeChecker\Check\register(\ImmanentCodeChecker\STAGE_FILE,
                                        'RegisterRejectsPatternWhitespace' . uniqid(),
                                        function () { return true; },
                                        '',
                                        ' *.php ');

  };

  expectException($cloTest, "\Exception");

}
