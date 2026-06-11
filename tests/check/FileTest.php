<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

const FILE_CHECK_TEST_PROJECT_PATH = __DIR__ . '/../explore/test-data/project';

/**
  * Register a file check that reports one file error for every received file.
  **/
function registerFileErrorCheck(string $strCheckName): void {

  \ImmanentCodeChecker\Check\register(\ImmanentCodeChecker\STAGE_FILE,
                                      $strCheckName,
                                      function (\ImmanentCodeChecker\DataObject $objFile) use ($strCheckName) {

                                        \ImmanentCodeChecker\Error\file($strCheckName,
                                                                        'expected file check error',
                                                                        $objFile);

                                      });

}

/**
  * Return all file errors reported by the given check.
  **/
function getFileErrorsByCheck(string $strCheckName): array {

  $objRegistry = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\ERROR_FILE);
  $arrErrors   = array();

  foreach($objRegistry->getAll() AS $objError)
    if($objError->get('check') === $strCheckName)
      $arrErrors[] = $objError;

  return $arrErrors;

}

/**
  * Expect file checks to report one error for every explored file.
  **/
function testFileCallsRegisteredCheckForEveryExploredFile() {

  $strCheckName   = 'FileCallsRegisteredCheckForEveryExploredFile';
  $strProjectPath = realpath(FILE_CHECK_TEST_PROJECT_PATH);

  \ImmanentCodeChecker\Explore\project(FILE_CHECK_TEST_PROJECT_PATH);
  registerFileErrorCheck($strCheckName);

  \ImmanentCodeChecker\Check\file();

  $arrErrors        = getFileErrorsByCheck($strCheckName);
  $arrExpectedPaths = array($strProjectPath . '/Readme.md',
                            $strProjectPath . '/src/Test.php');
  $arrErrorPaths    = array();

  foreach($arrErrors AS $objError)
    $arrErrorPaths[] = $objError->get('full_path');

  sort($arrExpectedPaths);
  sort($arrErrorPaths);

  assertEquals(count($arrErrors), 2);
  assertEquals($arrErrorPaths, $arrExpectedPaths);

}

/**
  * Expect file checks to receive file DataObjects with full and relative paths.
  **/
function testFilePassesFileDataObjectToCallback() {

  $strCheckName   = 'FilePassesFileDataObjectToCallback';
  $strProjectPath = realpath(FILE_CHECK_TEST_PROJECT_PATH);

  \ImmanentCodeChecker\Explore\project(FILE_CHECK_TEST_PROJECT_PATH);
  registerFileErrorCheck($strCheckName);

  \ImmanentCodeChecker\Check\file();

  $arrErrors     = getFileErrorsByCheck($strCheckName);
  $strReadmePath = $strProjectPath . '/Readme.md';
  $arrFound      = null;

  foreach($arrErrors AS $objError)
    if($objError->get('full_path') === $strReadmePath)
      $arrFound = $objError->getAll();

  assertEquals($arrFound['full_path'],     $strReadmePath);
  assertEquals($arrFound['relative_path'], 'Readme.md');
  assertEquals($arrFound['message'],       'expected file check error');

}

/**
  * Expect all() to execute registered file checks.
  **/
function testAllCallsFileChecks() {

  $strCheckName  = 'AllCallsFileChecks';
  $strProjectPath = realpath(FILE_CHECK_TEST_PROJECT_PATH);

  \ImmanentCodeChecker\Explore\project(FILE_CHECK_TEST_PROJECT_PATH);
  registerFileErrorCheck($strCheckName);

  \ImmanentCodeChecker\Check\all();

  $arrErrors     = getFileErrorsByCheck($strCheckName);
  $arrErrorPaths = array();

  foreach($arrErrors AS $objError)
    $arrErrorPaths[] = $objError->get('full_path');

  assertTrue(in_array($strProjectPath . '/Readme.md', $arrErrorPaths));
  assertTrue(in_array($strProjectPath . '/src/Test.php', $arrErrorPaths));

}

/**
  * Expect a file check with a pattern to only be called for matching files.
  **/
function testFileCheckWithPatternOnlyRunsForMatchingFiles() {

  $strCheckName   = 'FileCheckWithPatternOnlyRunsForMatchingFiles';
  $strProjectPath = realpath(FILE_CHECK_TEST_PROJECT_PATH);

  \ImmanentCodeChecker\Explore\project(FILE_CHECK_TEST_PROJECT_PATH);

  \ImmanentCodeChecker\Check\register(\ImmanentCodeChecker\STAGE_FILE,
                                      $strCheckName,
                                      function (\ImmanentCodeChecker\DataObject $objFile) use ($strCheckName) {

                                        \ImmanentCodeChecker\Error\file($strCheckName,
                                                                        'expected file check error',
                                                                        $objFile);

                                      },
                                      '',
                                      '*.php');

  \ImmanentCodeChecker\Check\file();

  $arrErrors     = getFileErrorsByCheck($strCheckName);
  $arrErrorPaths = array();

  foreach($arrErrors AS $objError)
    $arrErrorPaths[] = $objError->get('full_path');

  assertEquals(count($arrErrors), 1);
  assertEquals($arrErrorPaths, array($strProjectPath . '/src/Test.php'));

}

/**
  * Expect matching file parsers to run before file checks and to be discarded
  * after the file stage.
  **/
function testFileRunsMatchingParsersAndDiscardsResults() {

  $strCheckName   = 'FileRunsMatchingParsersAndDiscardsResults';
  $strParserName  = 'FileRunsMatchingParsersAndDiscardsResultsParser' . uniqid();
  $strProjectPath = realpath(FILE_CHECK_TEST_PROJECT_PATH);
  $arrParsedFiles = array();

  \ImmanentCodeChecker\Parser\register($strParserName,
                                       \ImmanentCodeChecker\PARSER_TYPE_FILE,
                                       function (string $strFilePath) use (&$arrParsedFiles) {

                                         $arrParsedFiles[] = $strFilePath;
                                         return basename($strFilePath);

                                       },
                                       'src/*.php');

  \ImmanentCodeChecker\Check\register(\ImmanentCodeChecker\STAGE_FILE,
                                      $strCheckName,
                                      function (\ImmanentCodeChecker\DataObject $objFile) use ($strCheckName, $strParserName) {

                                        if($objFile->get('relative_path') !== 'src/Test.php')
                                          return;

                                        $objParserResults = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\PARSER_RESULT);
                                        $arrParserResult  = $objParserResults->get($strParserName)->getAll();

                                        \ImmanentCodeChecker\Error\file($strCheckName,
                                                                        $arrParserResult['result'],
                                                                        $objFile);

                                      });

  \ImmanentCodeChecker\Explore\project(FILE_CHECK_TEST_PROJECT_PATH);
  \ImmanentCodeChecker\Check\file();

  $arrErrors       = getFileErrorsByCheck($strCheckName);
  $arrError        = $arrErrors[0]->getAll();
  $objParserResult = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\PARSER_RESULT);

  assertEquals($arrParsedFiles, array($strProjectPath . '/src/Test.php'));
  assertEquals(count($arrErrors), 1);
  assertEquals($arrError['message'], 'Test.php');
  assertEquals($objParserResult->getAll(), array());

}
