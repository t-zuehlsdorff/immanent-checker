<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

/**
  * Return a file DataObject that can be used as file error context.
  **/
function createFileErrorContext(): \ImmanentCodeChecker\DataObject {

  return new \ImmanentCodeChecker\DataObject(null,
                                             array('full_path'     => __FILE__,
                                                   'relative_path' => 'tests/error/FileTest.php',
                                                   'permissions'   => fileperms(__FILE__)));

}

/**
  * Expect file errors to be stored with check, message, file paths, and line.
  **/
function testFileErrorStoresExpectedData() {

  $objFile = createFileErrorContext();

  \ImmanentCodeChecker\Error\file('FileErrorStoresExpectedData',
                                  'expected file error',
                                  $objFile,
                                  12);

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\ERROR_FILE);

  $arrAllErrors = $objRegistry->getAll();

  assertEquals(count($arrAllErrors), 1);

  $objError = array_shift($arrAllErrors);
  $arrError = $objError->getAll();

  assertEquals($arrError['check'],         'FileErrorStoresExpectedData');
  assertEquals($arrError['message'],       'expected file error');
  assertEquals($arrError['full_path'],     __FILE__);
  assertEquals($arrError['relative_path'], 'tests/error/FileTest.php');
  assertEquals($arrError['line'],          12);

}

/**
  * Expect file errors to allow missing line information.
  **/
function testFileErrorAllowsMissingLine() {

  $objFile = createFileErrorContext();

  \ImmanentCodeChecker\Error\file('FileErrorAllowsMissingLine',
                                  'expected file error without line',
                                  $objFile);

  $objRegistry  = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\ERROR_FILE);
  $arrAllErrors = $objRegistry->getAll();

  assertEquals(count($arrAllErrors), 1);

  $objError = array_shift($arrAllErrors);
  $arrError = $objError->getAll();

  assertEquals($arrError['check'], 'FileErrorAllowsMissingLine');
  assertEquals($arrError['line'],  null);

}

/**
  * Expect file errors to reject empty check names.
  **/
function testFileErrorRejectsEmptyCheckName() {

  $objFile = createFileErrorContext();

  $cloTest = function () use ($objFile) {

    \ImmanentCodeChecker\Error\file('', 'expected file error', $objFile);

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect file errors to reject empty messages.
  **/
function testFileErrorRejectsEmptyMessage() {

  $objFile = createFileErrorContext();

  $cloTest = function () use ($objFile) {

    \ImmanentCodeChecker\Error\file('FileErrorRejectsEmptyMessage', '', $objFile);

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect file errors to reject line numbers below 1.
  **/
function testFileErrorRejectsInvalidLine() {

  $objFile = createFileErrorContext();

  $cloTest = function () use ($objFile) {

    \ImmanentCodeChecker\Error\file('FileErrorRejectsInvalidLine',
                                    'expected file error',
                                    $objFile,
                                    0);

  };

  expectException($cloTest, "\Exception");

}
