<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

const EXPLORE_TEST_PROJECT_PATH = __DIR__ . '/test-data/project';

/**
  * Expect project exploration to reject paths that do not exist.
  **/
function testProjectRejectsUnknownPath() {

  $cloTest = function () {

    \ImmanentCodeChecker\Explore\project(__DIR__ . '/test-data/missing');

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect project exploration to reject existing paths that are not directories.
  **/
function testProjectRejectsNonDirectoryPath() {

  $cloTest = function () {

    \ImmanentCodeChecker\Explore\project(__DIR__ . '/test-data/non-directory-path.txt');

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect the complete-project pool to contain every explored project entry.
  **/
function testProjectStoresCompleteProjectPool() {

  \ImmanentCodeChecker\Explore\project(EXPLORE_TEST_PROJECT_PATH);

  $strProjectPath = realpath(EXPLORE_TEST_PROJECT_PATH);

  $arrExpectedPaths = array($strProjectPath . '/Readme.md',
                            $strProjectPath . '/src',
                            $strProjectPath . '/src/Test.php');

  $objPool  = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\EXPLORE_COMPLETE_PROJECT);
  $arrFound = array();

  foreach($objPool->getAll() AS $objEntry)
    $arrFound[] = $objEntry->get('full_path');

  sort($arrExpectedPaths);
  sort($arrFound);

  assertEquals($arrFound, $arrExpectedPaths);

}

/**
  * Expect the project pool to contain every explored project entry by full path.
  **/
function testProjectStoresProjectPool() {

  \ImmanentCodeChecker\Explore\project(EXPLORE_TEST_PROJECT_PATH);

  $strProjectPath = realpath(EXPLORE_TEST_PROJECT_PATH);

  $arrExpectedPaths = array($strProjectPath . '/Readme.md',
                            $strProjectPath . '/src',
                            $strProjectPath . '/src/Test.php');

  $objPool  = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\EXPLORE_PROJECT);
  $arrFound = array_keys($objPool->getAll());

  sort($arrExpectedPaths);
  sort($arrFound);

  assertEquals($arrFound, $arrExpectedPaths);

}

/**
  * Expect the directory pool to contain only explored directories.
  **/
function testProjectStoresDirectoryPool() {

  \ImmanentCodeChecker\Explore\project(EXPLORE_TEST_PROJECT_PATH);

  $strProjectPath = realpath(EXPLORE_TEST_PROJECT_PATH);
  $strSourcePath  = $strProjectPath . '/src';

  $objPool  = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\EXPLORE_DIRECTORY);
  $arrEntry = $objPool->get($strSourcePath)->getAll();

  assertEquals(count($objPool->getAll()), 1);
  assertEquals($arrEntry['full_path'], $strSourcePath);
  assertEquals($arrEntry['relative_path'], 'src');

}

/**
  * Expect the file pool to contain only explored files.
  **/
function testProjectStoresFilePool() {

  \ImmanentCodeChecker\Explore\project(EXPLORE_TEST_PROJECT_PATH);

  $strProjectPath = realpath(EXPLORE_TEST_PROJECT_PATH);

  $arrExpected = array($strProjectPath . '/Readme.md'     => 'Readme.md',
                       $strProjectPath . '/src/Test.php' => 'src/Test.php');

  $objPool = new \ImmanentCodeChecker\DataObjectPool(\ImmanentCodeChecker\EXPLORE_FILE);

  assertEquals(count($objPool->getAll()), 2);

  foreach($arrExpected AS $strFullPath => $strRelativePath) {

    $arrEntry = $objPool->get($strFullPath)->getAll();

    assertEquals($arrEntry['full_path'], $strFullPath);
    assertEquals($arrEntry['relative_path'], $strRelativePath);

  }

}
