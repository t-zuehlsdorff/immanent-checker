<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

/**
  * Expect a registered check to be stored in the DataObjectPool of the selected
  * stage with its name, description, and callback unchanged.
  **/
function testRegisterStoresCheckInStageRegistry() {

  $strStage       = \ImmanentChecker\STAGE_COMPLETE_PROJECT;
  $strName        = 'RegisterCompleteProject' . uniqid();
  $strDescription = 'test check registration';
  $cloCallback    = function () { return true; };

  \ImmanentChecker\Check\register($strStage, $strName, $cloCallback, $strDescription);

  $objRegistry = new \ImmanentChecker\DataObjectPool($strStage);
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

  $strStage    = \ImmanentChecker\STAGE_PROJECT;
  $strName     = 'RegisterWithoutDescription' . uniqid();
  $cloCallback = function () { return true; };

  \ImmanentChecker\Check\register($strStage, $strName, $cloCallback);

  $objRegistry = new \ImmanentChecker\DataObjectPool($strStage);
  $arrCheck    = $objRegistry->get($strName)->getAll();

  assertEquals($arrCheck['description'], '');

}

/**
  * Expect registration to accept every known analysis stage.
  **/
function testRegisterAcceptsAllKnownStages() {

  $arrStages = array(\ImmanentChecker\STAGE_COMPLETE_PROJECT,
                     \ImmanentChecker\STAGE_PROJECT,
                     \ImmanentChecker\STAGE_DIRECTORY,
                     \ImmanentChecker\STAGE_FILE);

  foreach($arrStages AS $strStage) {

    $strName     = 'RegisterKnownStage' . uniqid();
    $cloCallback = function () { return true; };

    \ImmanentChecker\Check\register($strStage, $strName, $cloCallback);

    $objRegistry = new \ImmanentChecker\DataObjectPool($strStage);

    assertTrue($objRegistry->exists($strName));

  }

}

/**
  * Expect registration to reject stages that are not part of the known analysis
  * stages.
  **/
function testRegisterRejectsUnknownStage() {

  $cloTest = function () {

    \ImmanentChecker\Check\register('unknown-stage',
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

  $strStage    = \ImmanentChecker\STAGE_FILE;
  $strName     = 'RegisterDuplicateName' . uniqid();
  $cloCallback = function () { return true; };

  \ImmanentChecker\Check\register($strStage, $strName, $cloCallback);

  $cloTest = function () use ($strStage, $strName) {

    \ImmanentChecker\Check\register($strStage, $strName, function () { return true; });

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

  \ImmanentChecker\Check\register(\ImmanentChecker\STAGE_DIRECTORY,
                                      $strName,
                                      $cloCallback);

  \ImmanentChecker\Check\register(\ImmanentChecker\STAGE_FILE,
                                      $strName,
                                      $cloCallback);

  $objDirectoryRegistry = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\STAGE_DIRECTORY);
  $objFileRegistry      = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\STAGE_FILE);

  assertTrue($objDirectoryRegistry->exists($strName));
  assertTrue($objFileRegistry->exists($strName));

}

/**
  * Expect the stage registry validator to reject empty check names.
  **/
function testStageRegistryRejectsEmptyCheckName() {

  $cloTest = function () {

    \ImmanentChecker\Check\register(\ImmanentChecker\STAGE_FILE,
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

    \ImmanentChecker\Check\register(\ImmanentChecker\STAGE_FILE,
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

    \ImmanentChecker\Check\register(\ImmanentChecker\STAGE_FILE,
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

  $strStage    = \ImmanentChecker\STAGE_FILE;
  $strName     = 'RegisterUsesDefaultPattern' . uniqid();
  $cloCallback = function () { return true; };

  \ImmanentChecker\Check\register($strStage, $strName, $cloCallback);

  $objRegistry = new \ImmanentChecker\DataObjectPool($strStage);
  $arrCheck    = $objRegistry->get($strName)->getAll();

  assertEquals($arrCheck['pattern'], '*');

}

/**
  * Expect an explicit pattern to be stored in the check registration.
  **/
function testRegisterStoresExplicitPattern() {

  $strStage    = \ImmanentChecker\STAGE_FILE;
  $strName     = 'RegisterStoresExplicitPattern' . uniqid();
  $cloCallback = function () { return true; };

  \ImmanentChecker\Check\register($strStage, $strName, $cloCallback, '', '*.php');

  $objRegistry = new \ImmanentChecker\DataObjectPool($strStage);
  $arrCheck    = $objRegistry->get($strName)->getAll();

  assertEquals($arrCheck['pattern'], '*.php');

}

/**
  * Expect patterns to be accepted for directory checks.
  **/
function testRegisterAcceptsPatternForDirectoryStage() {

  $strStage    = \ImmanentChecker\STAGE_DIRECTORY;
  $strName     = 'RegisterAcceptsPatternForDirectory' . uniqid();
  $cloCallback = function () { return true; };

  \ImmanentChecker\Check\register($strStage, $strName, $cloCallback, '', 'src/*');

  $objRegistry = new \ImmanentChecker\DataObjectPool($strStage);
  $arrCheck    = $objRegistry->get($strName)->getAll();

  assertEquals($arrCheck['pattern'], 'src/*');

}

/**
  * Expect registration to reject a non-default pattern for the complete project
  * stage.
  **/
function testRegisterRejectsPatternForCompleteProjectStage() {

  $cloTest = function () {

    \ImmanentChecker\Check\register(\ImmanentChecker\STAGE_COMPLETE_PROJECT,
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

    \ImmanentChecker\Check\register(\ImmanentChecker\STAGE_PROJECT,
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

    \ImmanentChecker\Check\register(\ImmanentChecker\STAGE_FILE,
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

    \ImmanentChecker\Check\register(\ImmanentChecker\STAGE_FILE,
                                        'RegisterRejectsPatternWhitespace' . uniqid(),
                                        function () { return true; },
                                        '',
                                        ' *.php ');

  };

  expectException($cloTest, "\Exception");

}
