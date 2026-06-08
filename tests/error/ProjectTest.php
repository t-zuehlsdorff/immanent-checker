<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

/**
  * Expect project errors to be stored with check, message, files, and details.
  **/
function testProjectErrorStoresExpectedData() {

  \ImmanentCodeChecker\Error\project('ProjectErrorStoresExpectedData',
                                     'expected project error',
                                     array('src/Foo.php', 'tests/FooTest.php'),
                                     '{"function":"foo"}');

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\ERROR_PROJECT);

  $arrAllErrors = $objRegistry->getAll();

  assertEquals(count($arrAllErrors), 1);

  $objError = array_shift($arrAllErrors);
  $arrError = $objError->getAll();

  assertEquals($arrError['check'],    'ProjectErrorStoresExpectedData');
  assertEquals($arrError['message'],  'expected project error');
  assertEquals($arrError['files'][0], 'src/Foo.php');
  assertEquals($arrError['files'][1], 'tests/FooTest.php');
  assertEquals($arrError['details'],  '{"function":"foo"}');

}

/**
  * Expect project errors to use empty files and details by default.
  **/
function testProjectErrorAllowsMissingFilesAndDetails() {

  \ImmanentCodeChecker\Error\project('ProjectErrorAllowsMissingFilesAndDetails',
                                     'expected project error');

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\ERROR_PROJECT);

  $arrAllErrors = $objRegistry->getAll();

  assertEquals(count($arrAllErrors), 1);

  $objError = array_shift($arrAllErrors);
  $arrError = $objError->getAll();

  assertEquals($arrError['files'],   array());
  assertEquals($arrError['details'], '');

}

/**
  * Expect project errors to reject empty check names.
  **/
function testProjectErrorRejectsEmptyCheckName() {

  $cloTest = function () {

    \ImmanentCodeChecker\Error\project('', 'expected project error');

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect project errors to reject empty messages.
  **/
function testProjectErrorRejectsEmptyMessage() {

  $cloTest = function () {

    \ImmanentCodeChecker\Error\project('ProjectErrorRejectsEmptyMessage', '');

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect project errors to reject empty file references.
  **/
function testProjectErrorRejectsEmptyFileReference() {

  $cloTest = function () {

    \ImmanentCodeChecker\Error\project('ProjectErrorRejectsEmptyFileReference',
                                       'expected project error',
                                       array(''));

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect project errors to reject file references with surrounding whitespace.
  **/
function testProjectErrorRejectsFileReferenceWithSurroundingWhitespace() {

  $cloTest = function () {

    \ImmanentCodeChecker\Error\project('ProjectErrorRejectsFileReferenceWithSurroundingWhitespace',
                                       'expected project error',
                                       array(' src/Foo.php '));

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect project errors to reject invalid JSON details.
  **/
function testProjectErrorRejectsInvalidJsonDetails() {

  $cloTest = function () {

    \ImmanentCodeChecker\Error\project('ProjectErrorRejectsInvalidJsonDetails',
                                       'expected project error',
                                       array(),
                                       '{invalid');

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect project errors to reject JSON details with surrounding whitespace.
  **/
function testProjectErrorRejectsJsonDetailsWithSurroundingWhitespace() {

  $cloTest = function () {

    \ImmanentCodeChecker\Error\project('ProjectErrorRejectsJsonDetailsWithSurroundingWhitespace',
                                       'expected project error',
                                       array(),
                                       ' {"function":"foo"} ');

  };

  expectException($cloTest, "\Exception");

}
