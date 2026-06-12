<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

/**
  * Return a directory DataObject that can be used as directory error context.
  **/
function createDirectoryErrorContext(): \ImmanentChecker\DataObject {

  return new \ImmanentChecker\DataObject(null,
                                             array('full_path'     => __DIR__,
                                                   'relative_path' => 'tests/error',
                                                   'permissions'   => fileperms(__DIR__)));

}

/**
  * Expect directory errors to be stored with check, message, and directory paths.
  **/
function testDirectoryErrorStoresExpectedData() {

  $objDirectory = createDirectoryErrorContext();

  \ImmanentChecker\Error\directory('DirectoryErrorStoresExpectedData',
                                       'expected directory error',
                                       $objDirectory);

  $objRegistry = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\ERROR_DIRECTORY);

  $arrAllErrors = $objRegistry->getAll();

  assertEquals(count($arrAllErrors), 1);

  $objError = array_shift($arrAllErrors);
  $arrError = $objError->getAll();

  assertEquals($arrError['check'],         'DirectoryErrorStoresExpectedData');
  assertEquals($arrError['message'],       'expected directory error');
  assertEquals($arrError['full_path'],     __DIR__);
  assertEquals($arrError['relative_path'], 'tests/error');

}

/**
  * Expect directory errors to reject empty check names.
  **/
function testDirectoryErrorRejectsEmptyCheckName() {

  $objDirectory = createDirectoryErrorContext();

  $cloTest = function () use ($objDirectory) {

    \ImmanentChecker\Error\directory('', 'expected directory error', $objDirectory);

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect directory errors to reject empty messages.
  **/
function testDirectoryErrorRejectsEmptyMessage() {

  $objDirectory = createDirectoryErrorContext();

  $cloTest = function () use ($objDirectory) {

    \ImmanentChecker\Error\directory('DirectoryErrorRejectsEmptyMessage', '', $objDirectory);

  };

  expectException($cloTest, "\Exception");

}
