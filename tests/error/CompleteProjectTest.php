<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

/**
  * Expect complete-project errors to be stored with check, message, files, and
  * details.
  **/
function testCompleteProjectErrorStoresExpectedData() {

  \ImmanentCodeChecker\Error\completeProject('CompleteProjectErrorStoresExpectedData',
                                             'expected complete project error',
                                             array('vendor/autoload.php'),
                                             '{"directory":"vendor"}');

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\ERROR_COMPLETE_PROJECT);

  $arrAllErrors = $objRegistry->getAll();

  assertEquals(count($arrAllErrors), 1);

  $objError = array_shift($arrAllErrors);
  $arrError = $objError->getAll();

  assertEquals($arrError['check'],    'CompleteProjectErrorStoresExpectedData');
  assertEquals($arrError['message'],  'expected complete project error');
  assertEquals($arrError['files'][0], 'vendor/autoload.php');
  assertEquals($arrError['details'],  '{"directory":"vendor"}');

}

/**
  * Expect complete-project errors to use empty files and details by default.
  **/
function testCompleteProjectErrorAllowsMissingFilesAndDetails() {

  \ImmanentCodeChecker\Error\completeProject('CompleteProjectErrorAllowsMissingFilesAndDetails',
                                             'expected complete project error');

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\ERROR_COMPLETE_PROJECT);

  $arrAllErrors = $objRegistry->getAll();

  assertEquals(count($arrAllErrors), 1);

  $objError = array_shift($arrAllErrors);
  $arrError = $objError->getAll();

  assertEquals($arrError['files'],   array());
  assertEquals($arrError['details'], '');

}

/**
  * Expect complete-project errors to reject empty check names.
  **/
function testCompleteProjectErrorRejectsEmptyCheckName() {

  $cloTest = function () {

    \ImmanentCodeChecker\Error\completeProject('', 'expected complete project error');

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect complete-project errors to reject empty messages.
  **/
function testCompleteProjectErrorRejectsEmptyMessage() {

  $cloTest = function () {

    \ImmanentCodeChecker\Error\completeProject('CompleteProjectErrorRejectsEmptyMessage', '');

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect complete-project errors to reject empty file references.
  **/
function testCompleteProjectErrorRejectsEmptyFileReference() {

  $cloTest = function () {

    \ImmanentCodeChecker\Error\completeProject('CompleteProjectErrorRejectsEmptyFileReference',
                                               'expected complete project error',
                                               array(''));

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect complete-project errors to reject file references with surrounding
  * whitespace.
  **/
function testCompleteProjectErrorRejectsFileReferenceWithSurroundingWhitespace() {

  $cloTest = function () {

    \ImmanentCodeChecker\Error\completeProject('CompleteProjectErrorRejectsFileReferenceWithSurroundingWhitespace',
                                               'expected complete project error',
                                               array(' vendor/autoload.php '));

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect complete-project errors to reject invalid JSON details.
  **/
function testCompleteProjectErrorRejectsInvalidJsonDetails() {

  $cloTest = function () {

    \ImmanentCodeChecker\Error\completeProject('CompleteProjectErrorRejectsInvalidJsonDetails',
                                               'expected complete project error',
                                               array(),
                                               '{invalid');

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect complete-project errors to reject JSON details with surrounding
  * whitespace.
  **/
function testCompleteProjectErrorRejectsJsonDetailsWithSurroundingWhitespace() {

  $cloTest = function () {

    \ImmanentCodeChecker\Error\completeProject('CompleteProjectErrorRejectsJsonDetailsWithSurroundingWhitespace',
                                               'expected complete project error',
                                               array(),
                                               ' {"directory":"vendor"} ');

  };

  expectException($cloTest, "\Exception");

}
