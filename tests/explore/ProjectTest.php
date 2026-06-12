<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../../config.inc.php';

const EXPLORE_TEST_PROJECT_PATH = __DIR__ . '/test-data/project';

/**
  * Expect project exploration to reject paths that do not exist.
  **/
function testProjectRejectsUnknownPath() {

  $cloTest = function () {

    \ImmanentChecker\Explore\project(__DIR__ . '/test-data/missing');

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect project exploration to reject existing paths that are not directories.
  **/
function testProjectRejectsNonDirectoryPath() {

  $cloTest = function () {

    \ImmanentChecker\Explore\project(__DIR__ . '/test-data/non-directory-path.txt');

  };

  expectException($cloTest, "\Exception");

}

/**
  * Expect the complete-project pool to contain every explored project entry.
  **/
function testProjectStoresCompleteProjectPool() {

  \ImmanentChecker\Explore\project(EXPLORE_TEST_PROJECT_PATH);

  $strProjectPath = realpath(EXPLORE_TEST_PROJECT_PATH);

  $arrExpectedPaths = array($strProjectPath . '/Readme.md',
                            $strProjectPath . '/src',
                            $strProjectPath . '/src/Test.php');

  $objPool  = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_COMPLETE_PROJECT);
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

  \ImmanentChecker\Explore\project(EXPLORE_TEST_PROJECT_PATH);

  $strProjectPath = realpath(EXPLORE_TEST_PROJECT_PATH);

  $arrExpectedPaths = array($strProjectPath . '/Readme.md',
                            $strProjectPath . '/src',
                            $strProjectPath . '/src/Test.php');

  $objPool  = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_PROJECT);
  $arrFound = array_keys($objPool->getAll());

  sort($arrExpectedPaths);
  sort($arrFound);

  assertEquals($arrFound, $arrExpectedPaths);

}

/**
  * Expect the directory pool to contain only explored directories.
  **/
function testProjectStoresDirectoryPool() {

  \ImmanentChecker\Explore\project(EXPLORE_TEST_PROJECT_PATH);

  $strProjectPath = realpath(EXPLORE_TEST_PROJECT_PATH);
  $strSourcePath  = $strProjectPath . '/src';

  $objPool  = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_DIRECTORY);
  $arrEntry = $objPool->get($strSourcePath)->getAll();

  assertEquals(count($objPool->getAll()), 1);
  assertEquals($arrEntry['full_path'], $strSourcePath);
  assertEquals($arrEntry['relative_path'], 'src');

}

/**
  * Expect the file pool to contain only explored files.
  **/
function testProjectStoresFilePool() {

  \ImmanentChecker\Explore\project(EXPLORE_TEST_PROJECT_PATH);

  $strProjectPath = realpath(EXPLORE_TEST_PROJECT_PATH);

  $arrExpected = array($strProjectPath . '/Readme.md'    => 'Readme.md',
                       $strProjectPath . '/src/Test.php' => 'src/Test.php');

  $objPool = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_FILE);

  assertEquals(count($objPool->getAll()), 2);

  foreach($arrExpected AS $strFullPath => $strRelativePath) {

    $arrEntry = $objPool->get($strFullPath)->getAll();

    assertEquals($arrEntry['full_path'], $strFullPath);
    assertEquals($arrEntry['relative_path'], $strRelativePath);

  }

}

/**
  * Expect excluded files to remain in the complete-project pool and to be
  * missing from the filtered project and file pools.
  **/
function testProjectExcludesFileFromFilteredPools() {

  \ImmanentChecker\Explore\project(EXPLORE_TEST_PROJECT_PATH,
                                       array('Readme.md'));

  $strProjectPath = realpath(EXPLORE_TEST_PROJECT_PATH);
  $strReadmePath  = $strProjectPath . '/Readme.md';

  $objCompleteProject = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_COMPLETE_PROJECT);
  $objProject         = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_PROJECT);
  $objFile            = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_FILE);

  assertTrue($objCompleteProject->exists($strReadmePath));
  assertFalse($objProject->exists($strReadmePath));
  assertFalse($objFile->exists($strReadmePath));

}

/**
  * Expect exclude patterns to be matched against project-relative paths.
  **/
function testProjectExcludesByRelativePathPattern() {

  \ImmanentChecker\Explore\project(EXPLORE_TEST_PROJECT_PATH,
                                       array('src/*'));

  $strProjectPath = realpath(EXPLORE_TEST_PROJECT_PATH);
  $strSourcePath  = $strProjectPath . '/src';
  $strTestPath    = $strProjectPath . '/src/Test.php';

  $objProject = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_PROJECT);
  $objFile    = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_FILE);

  assertTrue($objProject->exists($strSourcePath));
  assertFalse($objProject->exists($strTestPath));
  assertFalse($objFile->exists($strTestPath));

}

/**
  * Expect excluded directories to be missing from the filtered directory pool.
  **/
function testProjectExcludesDirectoryFromFilteredPools() {

  \ImmanentChecker\Explore\project(EXPLORE_TEST_PROJECT_PATH,
                                       array('src'));

  $strProjectPath = realpath(EXPLORE_TEST_PROJECT_PATH);
  $strSourcePath  = $strProjectPath . '/src';

  $objCompleteProject = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_COMPLETE_PROJECT);
  $objProject         = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_PROJECT);
  $objDirectory       = new \ImmanentChecker\DataObjectPool(\ImmanentChecker\EXPLORE_DIRECTORY);

  assertTrue($objCompleteProject->exists($strSourcePath));
  assertFalse($objProject->exists($strSourcePath));
  assertFalse($objDirectory->exists($strSourcePath));

}
