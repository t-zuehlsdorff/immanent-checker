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
