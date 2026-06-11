<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

const DIRECTORY_CHECK_TEST_PROJECT_PATH = __DIR__ . '/../explore/test-data/project';

/**
  * Register a directory check that reports one directory error for every
  * received directory.
  **/
function registerDirectoryErrorCheck(string $strCheckName): void {

  \ImmanentCodeChecker\Check\register(\ImmanentCodeChecker\STAGE_DIRECTORY,
                                      $strCheckName,
                                      function (\ImmanentCodeChecker\DataObject $objDirectory) use ($strCheckName) {

                                        \ImmanentCodeChecker\Error\directory($strCheckName,
                                                                             'expected directory check error',
                                                                             $objDirectory);

                                      });

}

/**
  * Return all directory errors reported by the given check.
  **/
function getDirectoryErrorsByCheck(string $strCheckName): array {

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\ERROR_DIRECTORY);
  $arrErrors   = array();

  foreach($objRegistry->getAll() AS $objError)
    if($objError->get('check') === $strCheckName)
      $arrErrors[] = $objError;

  return $arrErrors;

}

/**
  * Expect directory checks to report one error for every explored directory.
  **/
function testDirectoryCallsRegisteredCheckForEveryExploredDirectory() {

  $strCheckName   = 'DirectoryCallsRegisteredCheckForEveryExploredDirectory';
  $strProjectPath = realpath(DIRECTORY_CHECK_TEST_PROJECT_PATH);

  \ImmanentCodeChecker\Explore\project(DIRECTORY_CHECK_TEST_PROJECT_PATH);
  registerDirectoryErrorCheck($strCheckName);

  \ImmanentCodeChecker\Check\directory();

  $arrErrors        = getDirectoryErrorsByCheck($strCheckName);
  $arrExpectedPaths = array($strProjectPath . '/src');
  $arrErrorPaths    = array();

  foreach($arrErrors AS $objError)
    $arrErrorPaths[] = $objError->get('full_path');

  assertEquals(count($arrErrors), 1);
  assertEquals($arrErrorPaths, $arrExpectedPaths);

}

/**
  * Expect directory checks to receive directory DataObjects with full and
  * relative paths.
  **/
function testDirectoryPassesDirectoryDataObjectToCallback() {

  $strCheckName   = 'DirectoryPassesDirectoryDataObjectToCallback';
  $strProjectPath = realpath(DIRECTORY_CHECK_TEST_PROJECT_PATH);

  \ImmanentCodeChecker\Explore\project(DIRECTORY_CHECK_TEST_PROJECT_PATH);
  registerDirectoryErrorCheck($strCheckName);

  \ImmanentCodeChecker\Check\directory();

  $arrErrors     = getDirectoryErrorsByCheck($strCheckName);
  $strSourcePath = $strProjectPath . '/src';
  $arrFound      = null;

  foreach($arrErrors AS $objError)
    if($objError->get('full_path') === $strSourcePath)
      $arrFound = $objError->getAll();

  assertEquals($arrFound['full_path'],     $strSourcePath);
  assertEquals($arrFound['relative_path'], 'src');
  assertEquals($arrFound['message'],       'expected directory check error');

}

/**
  * Expect a directory check with a pattern to only be called for matching
  * directories.
  **/
function testDirectoryCheckWithPatternOnlyRunsForMatchingDirectories() {

  $strCheckName   = 'DirectoryCheckWithPatternOnlyRunsForMatchingDirectories';
  $strProjectPath = realpath(DIRECTORY_CHECK_TEST_PROJECT_PATH);

  \ImmanentCodeChecker\Explore\project(DIRECTORY_CHECK_TEST_PROJECT_PATH);

  \ImmanentCodeChecker\Check\register(\ImmanentCodeChecker\STAGE_DIRECTORY,
                                      $strCheckName,
                                      function (\ImmanentCodeChecker\DataObject $objDirectory) use ($strCheckName) {

                                        \ImmanentCodeChecker\Error\directory($strCheckName,
                                                                             'expected directory check error',
                                                                             $objDirectory);

                                      },
                                      '',
                                      'nonexistent*');

  \ImmanentCodeChecker\Check\directory();

  $arrErrors = getDirectoryErrorsByCheck($strCheckName);

  assertEquals(count($arrErrors), 0);

}

/**
  * Expect all() to execute registered directory checks.
  **/
function testAllCallsDirectoryChecks() {

  $strCheckName   = 'AllCallsDirectoryChecks';
  $strProjectPath = realpath(DIRECTORY_CHECK_TEST_PROJECT_PATH);

  \ImmanentCodeChecker\Explore\project(DIRECTORY_CHECK_TEST_PROJECT_PATH);
  registerDirectoryErrorCheck($strCheckName);

  \ImmanentCodeChecker\Check\all();

  $arrErrors     = getDirectoryErrorsByCheck($strCheckName);
  $arrErrorPaths = array();

  foreach($arrErrors AS $objError)
    $arrErrorPaths[] = $objError->get('full_path');

  assertTrue(in_array($strProjectPath . '/src', $arrErrorPaths));

}
